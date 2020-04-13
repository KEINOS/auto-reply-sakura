<?php
namespace KEINOS\Tests;

use \KEINOS\Tests\TestCase;

/**
 * Main_GiveRegularDataTest.
 * ----------------------------------------------------------------------------
 * About this test:
 *   This test does the following:
 *     1. As a client's move, the "setUp()" method sends an email to the dummy
 *        SMTP server. Then it fetches back from the dummy SMTP's API.
 *     2. In the test method(s), it pipes the fetched raw email to the main
 *        script.
 *     3. The main script should send back (reply) to the email address of the
 *        client/user at the step #1.
 *     4. Finally it checks if the replied email was sent to the right address
 *        and right content by fetching the sent email from the dummy SMTP
 *        server.
 *   Note:
 *     Also note that this test assumes the dummy SMTP server be "mailcatcher"
 *     and is up in the same network and accessible to it's API as
 *     "http://smtp:1080/". Therefore it is recommended to use Docker and
 *     docker-compose to run the tests.
 * ----------------------------------------------------------------------------
 */
final class Main_GiveRegularDataTest extends TestCase
{
    // ========================================================================
    //  Properties
    // ========================================================================

     // Email address of a user/client who inquired and will receive the reply
    // email.
    public $email_user = 'i_am_your_user@sample.com';
    // Email address of a person in charge. Who receives the mail from the user
    // above.
    public $email_contact = 'to_whom_it_may_concern@my_domain.com';
    // Email address of the automat-reply. This address should be set in
    // "config/config.json".
    public $email_reply_bot;
    // ID to trace the inquired mail received
    public $id_user;
    // Mail header's break should be in CRLF
    public $CRLF = "\r\n";
    // Store sent inquiry raw mail
    public $email_inquiry_raw;

    // ========================================================================
    //  Tests
    // ========================================================================

    public function testReceiveMailAndPipeToTheMainScript()
    {
        $user_mail_raw    = $this->email_inquiry_raw;
        $name_script_main = 'auto-reply.php';
        $path_dir_root    = $this->getPathDirRootScript();
        $path_script_main = $path_dir_root . DIR_SEP . $name_script_main;

        if (! \file_exists($path_script_main)) {
            throw new \RuntimeException('File not found. Main script is missing at: ' . $path_script_main);
        }

        // Set time stamp to the date reply
        $time_stamp = 1586703600; // 2020/04/13 → Monday
        // Add slash to escape to include in command
        $user_mail_raw = \addslashes($user_mail_raw);
        // Create command
        $command = "/bin/echo '${user_mail_raw}' | IS_MODE_DEBUG=true TIME_CURRENT=${time_stamp} /usr/local/bin/php ${path_script_main}";
        // Run command externally
        $result_status = false;
        $result = $this->exec($command, $result_status);
        // In debug mode, the script returns the trace ID which was attached to the subject in the sent mail.
        $id_trace = \trim($result);
        $len_id   = \strlen(hash('md5', 'sample')); // Length of the ID to trace (MD5)

        if (! \ctype_xdigit($id_trace) || \strlen($id_trace) !== $len_id) {
            throw new \RuntimeException("Did not return the trace ID. Returned msg:\n" . $result);
        }
        // Get body actual
        $source      = $this->getMailSourceFromSubjectId($id_trace);
        $body_actual = trim($this->getMailTextBodyFromMailSource($source));
        $body_actual = \KEINOS\AutoMailReply\uniformEOL($body_actual);

        // Get body expect
        $path_dir_template  = $this->getPathDirTemplate();
        $name_file_template = 'reply_body.utf8.txt.sample'; // Template body when the debug mode is true
        $path_file_template = $path_dir_template . DIR_SEP . $name_file_template;
        $body_expect = \file_get_contents($path_file_template);
        $body_expect = \KEINOS\AutoMailReply\uniformEOL($body_expect);
        $body_expect = trim($body_expect . "(DEBUG ID: ${id_trace})");

        $this->assertSame($body_actual, $body_expect, "Reply body does not match to the one sent.\n---\n${body_actual}\n---\n${body_expect}\n---");
        // Check dummy SMTP server if the reply was sent
        //$this->assertTrue(($result_status === 0), 'Failed to execute command.');
    }

    // ========================================================================
    //  Methods
    // ========================================================================

    public function getCodeSample($time_zone)
    {
        $code = <<< 'CODE'
        namespace KEINOS\AutoMailReply;
        date_default_timezone_set('%%__TIME_ZONE__%%');
        require_once('./src/constants.php');
        require_once('./src/functions.php');
        echo (isTimeZoneAsiaTokyo() === true) ? 'true' : 'false';
CODE;
        $code = \str_replace('%%__TIME_ZONE__%%', $time_zone, $code);
        $code = \str_replace(PHP_EOL, '', $code);

        return $code;
    }

    public function getMailAllInJson()
    {
        $url_api_mailcatcher = 'http://smtp:1080/messages';
        $sent_messages = \file_get_contents($url_api_mailcatcher);
        if ($sent_messages === false) {
            throw new \RuntimeException("Fail to get list of sent messages from: ${url_api_mailcatcher}");
        }

        $sent_messages = json_decode($sent_messages);
        if ($sent_messages === false) {
            throw new \RuntimeException("Fail to parse the received JSON from: ${url_api_mailcatcher}");
        }
        return $sent_messages;
    }

    public function getMailSourceFromSubjectId($id_in_subject)
    {
        $sent_messages = $this->getMailAllInJson();

        // Search the sent inquiry mail
        foreach ($sent_messages as $message) {
            $subject = $message->subject;
            if (strpos($subject, $id_in_subject) === false) {
                continue;
            }
            // Get sent mail body from "mailcatcher" API
            $id_mail = $message->id;
            $source  = \file_get_contents("http://smtp:1080/messages/${id_mail}.source");
            if ($source === false) {
                throw new \RuntimeException("Fail to get #${id_mail} source/raw email data from: ${url_api_mailcatcher}");
            }

            return $source;
        }
        throw new \RuntimeException("Mail #${id_mail} not found from API. Searched Subject ID: ${id_in_subject}");
    }

    public function getMailSourceUserInquiry($id_in_subject)
    {
        // Get source/raw mail
        $source = $this->getMailSourceFromSubjectId($id_in_subject);
        // Set to property
        $this->email_inquiry_raw = $source;

        return $this->email_inquiry_raw;
    }

    public function getMailTextBodyFromMailSource($mail_source)
    {
        // Decode MIME mail and parse it to get body contents from raw mail data (from mail source)
        $mailParser  = new \ZBateson\MailMimeParser\MailMimeParser();
        $parsed_mail = $mailParser->parse($mail_source);

        return $parsed_mail->getTextContent();
    }

    public function isSmtpAlive()
    {
        $command = 'curl --head --silent http://smtp:1080/ | grep HTTP | grep 200\\ OK 1>/dev/null 2>/dev/null; echo $?';
        $last_line = \exec($command, $output, $return_var);

        return (trim($last_line) == '0');
    }

    public function setUp()
    {
        // Check if SMTP is up
        if (! $this->isSmtpAlive()) {
            throw new \RuntimeException('Can NOT connect to dummy SMTP server (mailcatcher): http://smpt:1080/');
        }
        // Create an ID to trace the inquiry mail from the user
        $this->id_user = hash('md5', time() . __FILE__);
        // User sends inquiry mail
        $result = $this->sendUserInquiryMail($this->id_user);
        if ($result === false) {
            throw new \RuntimeException('Failed to send dummy inquiring user mail.');
        }
        // Get and store the user sent mail to pipe through STDIN later
        $this->email_inquiry_raw = $this->getMailSourceUserInquiry($this->id_user);
    }

    public function sendUserInquiryMail($id_user)
    {
        // Create and send email. Pretend user sends email to inquire.
        $header_additional = [
            "From:{$this->email_user}",
            "Return-Path:{$this->email_user}", // Needs to be treated less as spam
            'MIME-Version:1.0',
            'Content-Transfer-Encoding:7bit',
            'Content-Type:text/plain; charset=ISO-2022-JP',
        ];
        $from_header      = \implode($this->CRLF, $header_additional);
        $param_additional = "-f{$this->email_user}";  // Needs to be treated less as spam

        $result = mb_send_mail(
            $this->email_contact,                          // to
            "This is a subject from the user. {$id_user}", // subject
            "This is a body from the user. [$id_user}",    // body
            $from_header,                                  // from
            $param_additional                              // set sender
        );

        return ($result !== false);
    }
}
