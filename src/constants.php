<?php
/**
 * Constants.
 * ========================================================
 * Note:
 *  This script must be loaded with functions and classes
 *  file before use.
 * Also note that these constants might be defined already
 * when testing by PHPUnit.
 *   See: ../tests/bootstrap/bootstrap.php
 */
namespace KEINOS\AutoMailReply;

defined('IS_MODE_DEBUG') or define('IS_MODE_DEBUG', isset($_ENV["IS_MODE_DEBUG"]) && ! empty($_ENV["IS_MODE_DEBUG"] && ($_ENV["IS_MODE_DEBUG"] === 'true')));

if (IS_MODE_DEBUG === true) {
    if (! defined('TIME_CURRENT') && isset($_ENV["TIME_CURRENT"])) {
        define('TIME_CURRENT', $_ENV["TIME_CURRENT"]);
    }
    if (! defined('NAME_FILE_CONF') && isset($_ENV["NAME_FILE_CONF"])) {
        define('NAME_FILE_CONF', $_ENV["NAME_FILE_CONF"]);
    }
    if (! defined('NAME_FILE_CONF')) {
        define('NAME_FILE_CONF', 'config.sample1_regular.json');
    }
    if (! defined('NAME_DIR_CONF')) {
        define('NAME_DIR_CONF', 'tests' . DIRECTORY_SEPARATOR . 'data_dummy');
    }
    if (! defined('NAME_FILE_REPLY')) {
        define('NAME_FILE_REPLY', 'reply_body.utf8.txt.sample');
    }
}

// Constants that might be changed by user or unit test
defined('MAIL_TITLE_DEFAULT')   or define('MAIL_TITLE_DEFAULT', 'お問い合わせありがとうございます。');
defined('NAME_FILE_LOG')        or define('NAME_FILE_LOG', 'error.log');
defined('NAME_FILE_REPLY')      or define('NAME_FILE_REPLY', 'reply_body.utf8.txt');
defined('NAME_FILE_MAIL_DUMMY') or define('NAME_FILE_MAIL_DUMMY', 'sample1.mail');
defined('NAME_FILE_CONF')       or define('NAME_FILE_CONF', 'config.json');     // Conf file must be in JSON format and UTF-8.
defined('NAME_DIR_MAIL_DUMMY')  or define('NAME_DIR_MAIL_DUMMY', 'data_dummy'); // Sample mail for test
defined('NAME_DIR_TEMPLATE')    or define('NAME_DIR_TEMPLATE', 'template');
defined('NAME_DIR_CONF')        or define('NAME_DIR_CONF', 'config');
defined('NAME_DIR_TEST')        or define('NAME_DIR_TEST', 'tests');

// Set current time (Avoid time diff when delay)
defined('TIME_CURRENT') or define('TIME_CURRENT', time());

// Constants that are basically should not changed
defined('PATH_DIR_SCRIPT') or define('PATH_DIR_SCRIPT', dirname(__DIR__)); // Root path of the repo

// Constants (Do not change)
const DIR_SEP = DIRECTORY_SEPARATOR;
const CRLF    = "\r\n";
const FAILURE = 1;
const SUCCESS = 0;
const PATH_DIR_TEMPLATE    = PATH_DIR_SCRIPT . DIR_SEP . NAME_DIR_TEMPLATE;
const PATH_FILE_CONF       = PATH_DIR_SCRIPT . DIR_SEP . NAME_DIR_CONF . DIR_SEP . NAME_FILE_CONF;
const PATH_DIR_SAMPLE_MAIL = PATH_DIR_SCRIPT . DIR_SEP . NAME_DIR_TEST . DIR_SEP . NAME_DIR_MAIL_DUMMY;
