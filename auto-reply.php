#!/usr/local/bin/php
<?php
/**
 * Main Script.
 * ============================================================================
 * Note:
 *  - This script must be compatible with PHP 5.6.40 and later.
 *  - To activate debug mode, which displays errors, set the env variable
 *    "IS_MODE_DEBUG=true" before running the script.
 *      - e.g.: $ IS_MODE_DEBUG=true php /path/to/auto-reply.php
 *  - On production this script SHOULD exit with a status 0 (zero) and
 *    any error/exception should be caught and logged.
 */
namespace KEINOS\AutoMailReply;

// Disable this only if needed. To control debug mode, use
// 'IS_MODE_DEBUG' env value instead.
if (true) {
    \error_reporting(E_ALL);
    \ini_set("display_errors", 1);
}

try {
    // Include packages
    require_once(__DIR__ . '/vendor/autoload.php');

    // Ensure internal language as JA-JP UTF-8
    \mb_language("Japanese");
    \mb_internal_encoding("UTF-8");
    // Change working directory
    \chdir(__DIR__);
    // Log for debugging
    $log_array = [];

    $id_trace = \hash('md5', \microtime() . __FILE__);

    if (isModeDebug()) {
        $log_array[] = 'Mode: Debug mode.';
        $log_array[] = 'Current Time: ' . TIME_CURRENT;
        $log_array[] = 'Weekday: ' . \date('l', TIME_CURRENT);
        $log_array[] = 'Path of conf file: ' . PATH_FILE_CONF;
        $log_array[] = 'ID to trace: ' . $id_trace;
    }

    // Read conf file
    $conf = new ConfigInfo(PATH_FILE_CONF);

    // Skip/do nothing if not holly day
    if (! $conf->isWeekdayToReply(TIME_CURRENT)) {
        if (isModeDebug()) {
            $msg_error = 'Did NOT reply: Date is a working day. See the configuration file.';
            $log_array[] = $msg_error;
            echo $msg_error, PHP_EOL;
            // On debug mode exit as fail(1) to let the test catch
            exit(FAILURE);
        }
        // On production the script should exit as success(0).
        // Otherwise the mailer won't proceed their part.
        exit(SUCCESS);
    }

    // Get mail title/subject to reply
    $subject = $conf->getSubject();
    if (isModeDebug()) {
        $subject = "${subject} (DEBUG ID: ${id_trace})";
    }
    // Get mail address of "From" to reply (Sender)
    $from = $conf->getEmailFrom();
    // Get user's email address to send reply (Recipients)
    $mail_received = getContentsMailReceived();
    $to = getMailAddressToReply($mail_received);

    // Get mail body to reply
    $body = getBodyReply(NAME_FILE_REPLY);
    if (isModeDebug()) {
        $body = $body . PHP_EOL . "(DEBUG ID: ${id_trace})" . PHP_EOL;
    }

    // Send Mail
    $result = sendReply([
        'from'    => $from,
        'to'      => $to,
        'subject' => $subject,
        'body'    => $body,
        'headers' => [
            'MIME-Version:1.0',
            'Content-Transfer-Encoding:7bit',
            'Content-Type:text/plain; charset=ISO-2022-JP'
        ],
    ]);
    if ($result === false) {
        $msg_error = "Failed to reply message: \n - From: ${from} \n - To: ${to} \n - Subject: ${subject} \n - Body: ${body}";
        $log_array[] = $msg_error;
        throw new \RuntimeException($msg_error);
    }
    if (isModeDebug()) {
        echo $id_trace . PHP_EOL;
    }

    exit(SUCCESS);
} catch (\RuntimeException $e) {
    // Get error
    $msg_error = $e->getMessage() . PHP_EOL;
    // Save log
    if (isModeDebug()) {
        $log_array[] = 'ERROR: Fail to execute script with message: ' . $msg_error;
    }
    if (! empty($log_array)) {
        $msg_log = \json_encode($log_array, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        saveLogs($msg_log);
    }

    exit(FAILURE);
}
