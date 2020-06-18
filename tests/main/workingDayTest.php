<?php

/**
 * ----------------------------------------------------------------------------
 * Test for "working day" (Do NOT reply automatically)
 *
 * This script tests the functionability of operating day which should NOT
 * reply the automated email.
 * ----------------------------------------------------------------------------
 */

namespace KEINOS\Tests;

use \KEINOS\Tests\TestCase;

final class Main_WorkingDayTest extends TestCase
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
            $msg = 'File not found. Main script is missing at: ' . $path_script_main;
            throw new \RuntimeException($msg);
        }

        // Set time stamp to the date received
        $time_stamp = \strtotime('2020/04/13 10:30'); // Monday before 18:00

        // Add slash to escape to include in command
        $user_mail_raw = \addslashes($user_mail_raw);

        // Create command
        $cmd_echo_mail_raw = "/bin/echo '${user_mail_raw}'";
        $cmd_run_script = "IS_MODE_DEBUG=true TIME_CURRENT=${time_stamp} /usr/local/bin/php ${path_script_main}";
        $command = "${cmd_echo_mail_raw} | ${cmd_run_script}";

        // Run command externally
        $result_status = false;
        $this->expectException(\RuntimeException::class);
        $result_msg = $this->exec($command, $result_status);
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
