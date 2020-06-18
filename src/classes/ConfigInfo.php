<?php
/**
 * ConfigInfo Class.
 * ========================================================
 * Note:
 *  This script must be compatible with PHP 5.6.40 and
 *  later.
 */
namespace KEINOS\AutoMailReply;

class ConfigInfo
{
    // Property
    private $conf_obj; // Stores JSON object from the conf file
    private $list_weekday_name_to_reply = [];
    private $list_weekday_object = [];

    public function __construct($path_file_config)
    {
        $this->conf_obj = $this->getObjConfig($path_file_config);

        // Validates configuration and sets properties
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
        return $this->list_weekday_name_to_reply;
    }

    /**
     * Alias function of is_iterable() in PHP7.
     *
     * @param  mixed $obj
     * @return bool
     */
    private function isIterable($obj)
    {
        return is_array($obj) || (is_object($obj) && ($obj instanceof \Traversable));
    }

    public function isWeekdayToReply($time_stamp)
    {
        // Weekday check
        $weekday_needle   = getWeekday($time_stamp);
        $weekday_haystack = $this->getWeekdaysToReply();

        if (! in_array($weekday_needle, $weekday_haystack)) {
            return false;
        }

        // Hour/min check within the weekday.
        // If needle exists in haystack then should exist in $this->list_weekdays_to_reply
        // array of objects as well.
        $format = 'H:i';
        $time_needle = date($format, $time_stamp);
        $obj_weekday = $this->list_weekday_object[$weekday_needle];

        return $obj_weekday->isTimeInBetween($time_needle);
    }

    public function validateWeekday()
    {
        if (! isset($this->conf_obj->weekday_to_reply)) {
            return [];
        }

        if (empty($this->conf_obj->weekday_to_reply)) {
            return [];
        }

        $weekdays = $this->conf_obj->weekday_to_reply;

        if (\is_string($weekdays)) {
            throw new \RuntimeException('Invalid format. "weekday_to_reply" elements must be in array. String given.');
        }

        if (! $this->isIterable($weekdays)) {
            throw new \RuntimeException('Invalid format. "weekday_to_reply" elements must be in JSON array.');
        }

        foreach ($weekdays as $value) {
            if (! isset($value->weekday)) {
                throw new \RuntimeException('Invalid format. "Weekday" key is missing.');
            }

            $weekday    = $value->weekday;
            $time_begin = isset($value->begin) ? $value->begin : '';
            $time_end   = isset($value->end)   ? $value->end   : '';

            $obj_weekday = new \KEINOS\AutoMailReply\WeekdayToReply($weekday, $time_begin, $time_end);

            // Set objects to internal array and return array.
            $this->list_weekday_name_to_reply[]  = $weekday;
            $this->list_weekday_object[$weekday] = $obj_weekday;
        }
    }
}
