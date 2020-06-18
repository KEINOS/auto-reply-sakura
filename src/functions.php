<?php
/**
 * Functions.
 * ========================================================
 * Note:
 *  This script must be compatible with PHP 5.6.40 and
 *  later.
 */
namespace KEINOS\AutoMailReply;

function formatTimeForLogging($time_stamp)
{
    // Format: https://www.php.net/manual/ja/function.date.php
    $format = 'Y/m/d H:i (l):e';

    return date($format, $time_stamp);
}

function getBodyReply($name_file_reply)
{
    $path_file_reply = PATH_DIR_TEMPLATE . DIR_SEP . $name_file_reply;
    if (! \file_exists($path_file_reply)) {
        $msg_error="Reply template not found at: ${path_file_reply} @ Line:" . __LINE__;
        throw new \RuntimeException($msg_error);
    }
    $mail_contents = \file_get_contents($path_file_reply);
    if ($mail_contents === false) {
        $msg_error="Error while reading the reply template file at: ${path_file_reply} @ Line:" . __LINE__;
        throw new \RuntimeException($msg_error);
    }

    $mail_contents = PHP_EOL . trim($mail_contents);

    return $mail_contents;
}

function getContentsFrom($input_stream)
{
    try {
        $contents = file_get_contents($input_stream);
        if ($contents === false) {
            $msg_error="Failed read contents from: ${input_stream} Line:" . __LINE__;
            throw new \RuntimeException($msg_error);
        }
    } catch (\Exception $e) {
        $msg_error="Exception error. Failed read contents from: ${input_stream} Line:" . __LINE__;
        throw new \RuntimeException($msg_error);
    }

    // Needs to log?
    //file_put_contents('./mail_received.txt', $contents . PHP_EOL, LOCK_EX | FILE_APPEND);

    // Convert all line breaks to same line endings as PHP_EOL
    return uniformEOL($contents);
}

function getContentsFromStdin()
{
    return getContentsFrom('php://stdin');
}

function getContentsMailReceived()
{
    if (isModeDebug()) {
        return getMailDummy();
    }

    return getContentsFromStdin();
}

function getCurrentTime()
{
    return TIME_CURRENT;
}

function getHourAndMinute($time_stamp)
{
    if (! \is_numeric($time_stamp)) {
        return false;
    }
    // Format: https://www.php.net/manual/ja/function.date.php
    $format = 'H:i';
    $hour_and_min = date($format, $time_stamp);
    if ($hour_and_min === false) {
        $msg_error="Malformed time stamp given. Timestamp: ${time_stamp}, Line:" . __LINE__;
        throw new \RuntimeException($msg_error);
    }

    return $hour_and_min;
}

function getMailAddressToReply($mail_received)
{
    $mailParser = new \ZBateson\MailMimeParser\MailMimeParser();
    $message = $mailParser->parse($mail_received);
    if ($message->getHeader('from') === null) {
        $msg_error='Failed to parse mail. Invalid contents given. Line:' . __LINE__;
        throw new \RuntimeException($msg_error);
    }
    $email_user = $message->getHeaderValue('from');

    return $email_user;
}

function getMailDummy($name_file_mail = null)
{
    if ($name_file_mail === null) {
        $name_file_mail = NAME_FILE_MAIL_DUMMY;
    }

    $path_file_mail = PATH_DIR_SAMPLE_MAIL . DIR_SEP . $name_file_mail;
    if (! \file_exists($path_file_mail)) {
        $msg_error="File not found at: ${path_file_mail} Line:" . __LINE__;
        throw new \RuntimeException($msg_error);
    }

    return getContentsFrom($path_file_mail);
}

function getWeekday($time_stamp)
{
    if (! \is_numeric($time_stamp)) {
        $msg_error = 'Malformed time stamp given.' . PHP_EOL
                   . '  Timestamp: ' . $time_stamp . PHP_EOL
                   . '  Line: ' . __LINE__;
        throw new \RuntimeException($msg_error);
    }

    // Format: https://www.php.net/manual/ja/function.date.php
    $format  = 'l';
    $weekday = date($format, $time_stamp);
    if ($weekday === false) {
        $msg_error = 'Malformed time stamp given.' . PHP_EOL
                   . '  Timestamp: ' . $time_stamp . PHP_EOL
                   . '  Line: ' . __LINE__;
        throw new \RuntimeException($msg_error);
    }

    return $weekday;
}

function isModeDebug()
{
    return (IS_MODE_DEBUG === true);
}

function isTimeZoneAsiaTokyo()
{
    // Format: https://www.php.net/manual/ja/function.date.php
    $format = 'e';
    return (date($format) === 'Asia/Tokyo');
}

function saveLogs($msg_error)
{
    $options       = LOCK_EX | FILE_APPEND; // Lock and do not overwrite but add(append)
    $path_file_log = PATH_DIR_SCRIPT . DIR_SEP . NAME_FILE_LOG; // Place log into current dir
    $msg_error .= PHP_EOL; // Add break in case of appending logs

    $result = file_put_contents($path_file_log, $msg_error, $options);

    return ($result !== false);
}

function sendReply($array)
{
    if (! isset($array['from']) || empty(trim($array['from']))) {
        return false;
    }
    if (! isset($array['to']) || empty(trim($array['to']))) {
        return false;
    }
    if (! isset($array['subject']) || empty(trim($array['subject']))) {
        return false;
    }
    if (! isset($array['body']) || empty(trim($array['body']))) {
        return false;
    }
    if (! isset($array['headers'])) {
        $array['headers'] = [];
    }

    // Basic mail info
    $from    = $array['from'];
    $to      = $array['to'];
    $subject = $array['subject'];
    $body    = $array['body'];
    $headers = $array['headers'];
    // "from" goes to header
    $headers[] = "From:${from}";
    $headers[] = "Return-Path:${from}";

    $additional_headers   = implode(CRLF, $headers);
    $additional_parameter = "-f${from}";

    // Send mail
    $result = mb_send_mail($to, $subject, $body, $additional_headers, $additional_parameter);
    if ($result === false) {
        $msg_error='Failed to send mail. Line:' . __LINE__;
        throw new \RuntimeException($msg_error);
    }
    return $result;
}

function uniformEOL($string, $to = PHP_EOL)
{
    return strtr($string, array(
        "\r\n" => $to,
        "\r"   => $to,
        "\n"   => $to,
    ));
}
