<?php
namespace KEINOS\Tests;

use \KEINOS\Tests\TestCase;

final class Function_SendReplyTest extends TestCase
{
    // ========================================================================
    //  Methods (Tests follows)
    // ========================================================================

    public function getBodyFromSource($source)
    {
        $mailParser  = new \ZBateson\MailMimeParser\MailMimeParser();
        $parsed_mail = $mailParser->parse($source);

        return $parsed_mail->getTextContent();
    }

    public function getTimeStamp()
    {
        static $time_stamp;
        if(isset($time_stamp)){
            return $time_stamp;
        }

        $format     = 'Y/m/d H:i (l):e';
        $time_stamp = date($format, time());
        return $time_stamp;
    }

    public function isSmtpAlive()
    {
        $command = 'curl --head --silent http://smtp:1080/ | grep HTTP | grep 200\\ OK 1>/dev/null 2>/dev/null; echo $?';
        $last_line = \exec($command, $output, $return_var);

        return (trim($last_line) == '0');
    }

    public function setUp()
    {
        // Pre-processing before each test method
        if (! $this->isSmtpAlive()) {
            $this->markTestSkipped('** Skipped **: Dummy SMTP server "http://smtp:1080" for testing is not up.');
        }
    }

    // ========================================================================
    //  Tests
    // ========================================================================

    public function testHeadersNotSet()
    {
        $mail_info_no_headers = [
            'from'    => 'from_me@sample.com',
            'to'      => 'to_me@sample.com',
            'subject' => 'タイトルです ' . $this->getTimeStamp(),
            'body'    => 'これは本文です。',
        ];
        // Send message to dummy SMTP server
        $result = \KEINOS\AutoMailReply\sendReply($mail_info_no_headers);
        $this->assertTrue($result, '"headers" element is not mandatory.');
    }

    public function testMustInfoIsEmpty()
    {
        $list_must_elements = [
            'from'    => 'from_me@sample.com',
            'to'      => 'to_me@sample.com',
            'subject' => 'タイトルです ' . $this->getTimeStamp(),
            'body'    => 'これは本文です。',
        ];
        foreach ($list_must_elements as $key => $value) {
            $temp_list = $list_must_elements;
            $temp_list[$key] = '';
            $mail_info = [
                'from'    => $temp_list['from'],
                'to'      => $temp_list['to'],
                'subject' => $temp_list['subject'],
                'body'    => $temp_list['body'],
                'headers' => [],
            ];

            // Send message to dummy SMTP server
            $result = \KEINOS\AutoMailReply\sendReply($mail_info);
            $this->assertFalse($result, "If \"${key}\" is empty, it should return false and do not send mail.");
        }
    }

    public function testMustInfoNotSet()
    {
        $list_must_elements = [
            'from'    => 'from_me@sample.com',
            'to'      => 'to_me@sample.com',
            'subject' => 'タイトルです ' . $this->getTimeStamp(),
            'body'    => 'これは本文です。',
        ];
        foreach ($list_must_elements as $key => $value) {
            $temp_list = $list_must_elements;

            unset($temp_list[$key]);

            // Send message to dummy SMTP server
            $result = \KEINOS\AutoMailReply\sendReply($temp_list);
            $this->assertFalse($result, "If \"${key}\" is not set, it should return false and do not send mail.");
        }
    }

    public function testSentMessageBodySameToReceived()
    {
        // Create data to trace the sent mail
        $hash_id     = hash('md5', time() . __FILE__);
        $sent_date   = $this->getTimeStamp();
        $body_expect = "これは本文です。 Hash ID: ${hash_id}   Sent Data: ${sent_date}";

        $mail_info   = [
            'from'    => 'from_me@sample.com',
            'to'      => 'to_me@sample.com',
            'subject' => 'タイトルです ' . $hash_id,
            'body'    => $body_expect,
            'headers' => [],
        ];
        // Send message to dummy SMTP server
        $result = \KEINOS\AutoMailReply\sendReply($mail_info);
        if ($result !== true) {
            $this->assertTrue($result, 'Failed to send dummy mail.');
        }

        // Fetch mails sent to dummy SMTP server
        $messages = json_decode(file_get_contents('http://smtp:1080/messages'));
        if ($messages === false) {
            $this->assertTrue(false, 'Failed to fetch sent mails from SMTP server.');
        }

        // Find sent mail from fetched mails
        $body_actual = '';
        foreach ($messages as $message) {
            $subject = $message->subject;
            if (strpos($subject, $hash_id) === false) {
                continue;
            }
            // Get sent mail body
            $id_mail = $message->id;
            $mail_source = file_get_contents("http://smtp:1080/messages/${id_mail}.source");
            $body_actual = $this->getBodyFromSource($mail_source);
        }

        $this->assertSame($body_expect, $body_expect, 'Sent mail does not match with original.');
    }

    public function testSimpleUsage()
    {
        $mail_info = [
            'from'    => 'from_me@sample.com',
            'to'      => 'to_me@sample.com',
            'subject' => 'タイトルです ' . $this->getTimeStamp(),
            'body'    => 'これは本文です。',
            'headers' => [],
        ];

        $result = \KEINOS\AutoMailReply\sendReply($mail_info);
        $this->assertTrue($result, "Failed to send mail");
    }
}
