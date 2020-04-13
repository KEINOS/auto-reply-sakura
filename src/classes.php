<?php
/**
 * Classes.
 * ========================================================
 * Note:
 *  This script must be compatible with PHP 5.6.40 and
 *  later.
 */
namespace KEINOS\AutoMailReply;

class ConfigInfo
{
    private $conf_obj;

    public function __construct($path_file_config)
    {
        $this->conf_obj = $this->getObjConfig($path_file_config);
        $this->validateWeekday();
    }

    public function getEmailFrom()
    {
        if (! isset($this->conf_obj->from->email)) {
            return '';
        }
        $email = $this->conf_obj->from->email;
        if (empty(trim($email))) {
            return '';
        }

        return $email;
    }

    public function getNameFrom()
    {
        if (! isset($this->conf_obj->from->name)) {
            return '';
        }
        $name = $this->conf_obj->from->name;
        if (empty(trim($name))) {
            return '';
        }

        return $name;
    }

    private function getObjConfig($path_file_config)
    {
        if (! \file_exists($path_file_config)) {
            $msg_error="Configuration file not found at: ${path_file_config} @ Line:" . __LINE__;
            throw new \RuntimeException($msg_error);
        }

        $conf_json = \file_get_contents($path_file_config);
        if ($conf_json === false) {
            $msg_error="Error while reading conf file at: ${path_file_config} @ Line:" . __LINE__;
            throw new \RuntimeException($msg_error);
        }

        $conf_obj = \json_decode($conf_json);
        if ($conf_obj === false) {
            $msg_error="Malformed JSON. Error while parsing conf file at: ${path_file_config} @ Line:" . __LINE__;
            throw new \RuntimeException($msg_error);
        }

        return $conf_obj;
    }

    public function getSubject()
    {
        if (! isset($this->conf_obj->mail_title_to_reply)) {
            return MAIL_TITLE_DEFAULT;
        }
        $title = $this->conf_obj->mail_title_to_reply;
        if (empty(trim($title))) {
            return MAIL_TITLE_DEFAULT;
        }

        return $title;
    }

    public function getWeekdaysToReply()
    {
        if (! isset($this->conf_obj->weekday_to_reply)) {
            return [];
        }

        $weekdays = $this->conf_obj->weekday_to_reply;

        if (\is_array($weekdays)) {
            return $weekdays;
        }
        if (\is_string($weekdays)) {
            $array = \explode(',', $weekdays);
            return array_map('trim', $array);
        }

        throw new \RuntimeException('"weekday_to_reply" element must be array or comma separated string.');
    }

    public function isValidWeekday($weekday_needle)
    {
        $weekday_haystack = [
            'Sunday',
            'Monday',
            'Tuesday',
            'Wednesday',
            'Thursday',
            'Friday',
            'Saturday',
        ];

        return in_array($weekday_needle, $weekday_haystack);
    }

    public function isWeekdayToReply($time_stamp)
    {
        $weekday_needle   = getWeekday($time_stamp);
        $weekday_haystack = $this->getWeekdaysToReply();

        return in_array($weekday_needle, $weekday_haystack);
    }

    public function validateWeekday()
    {
        $weekdays = $this->getWeekdaysToReply();

        foreach ($weekdays as $weekday) {
            if ($this->isValidWeekday($weekday) === false) {
                $msg_error="Mal-format conf file. Weekday description is not valid: ${weekday}";
                throw new \RuntimeException($msg_error);
            }
        }

        return true;
    }
}
