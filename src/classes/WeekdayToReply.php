<?php
/**
 * Item class of weekday.
 * ========================================================
 * This class holds weekday information to reply. Such as:
 *   1. Weekday name
 *   2. Begin time to replay in that weekday. Default:  0:00
 *   3. End time to reply in that weekday.    Default: 23:59
 *
 * Note:
 *  This script must be compatible with PHP 5.6.40 and later.
 *
 */
namespace KEINOS\AutoMailReply;

class WeekdayToReply
{
    private $weekday;
    private $time_begin_str;
    private $time_begin_int;
    private $time_end_str;
    private $time_end_int;

    const TIME_BEGIN_DEFAULT = '00:00';
    const TIME_END_DEFAULT   = '23:59';

    public function __construct($weekday, $time_begin='', $time_end='')
    {
        // Verify weekday name
        if (! $this->isValidWeekday($weekday)) {
            throw new \RuntimeException('Invalid weekday name.');
        }

        // Check and/or set default value
        if (empty(trim($time_begin))) {
            $time_begin = self::TIME_BEGIN_DEFAULT;
        }
        if (empty(trim($time_end))) {
            $time_end = self::TIME_END_DEFAULT;
        }

        // Check if the time is in range and well formatted
        $this->validateTime($time_begin);
        $this->validateTime($time_end);

        // Check if the time is consistent
        if (! $this->isTimeSmallerThan($time_begin, $time_end)) {
            throw new \RuntimeException('Invalid time. End time is earlier than begin time.');
        }

        // Set properties in string
        $this->weekday = $weekday;
        $this->time_begin_str = $time_begin;
        $this->time_end_str   = $time_end;
        // Set time property in integer. String "13:10" becomes integer 1310
        $this->time_begin_int = intval(str_replace(':', '', $time_begin));
        $this->time_end_int   = intval(str_replace(':', '', $time_end));
    }

    public function isTimeInBetween($time_hour_and_min)
    {
        $this->validateTime($time_hour_and_min);

        // Convert string in 'H:i' format time to integer
        $time_int = intval(str_replace(':', '', $time_hour_and_min));

        if ($time_int < $this->time_begin_int) {
            return false;
        }

        if ($time_int > $this->time_end_int) {
            return false;
        }

        return true;
    }

    public function isTimeSmallerThan($time_begin, $time_end)
    {
        if (empty(trim($time_begin))) {
            throw new \RuntimeException('Time not set. Empty "begin" time given.');
        }

        if (empty(trim($time_end))) {
            throw new \RuntimeException('Time not set. Empty "end" time given.');
        }

        // Convert string in 'H:i' format time to integer
        $int_begin = intval(str_replace(':', '', $time_begin));
        $int_end   = intval(str_replace(':', '', $time_end));

        return ($int_begin < $int_end);
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

    public function validateTime($time_hour_and_min)
    {
        if (empty(trim($time_hour_and_min))) {
            throw new \RuntimeException('Empty time is not allowed.');
        }

        if (substr_count($time_hour_and_min, ':') !== 1) {
            $msg = 'Bad format time. Should be something like "13:45" (hh:mm, 24 hour format). '
                 . 'Given: ' . $time_hour_and_min;
            throw new \RuntimeException($msg);
        }

        $reg = '/^(0[0-9]{1}|1{1}[0-9]{1}|2{1}[0-3]{1}):(0[0-9]{1}|[1-5]{1}[0-9]{1})$/';
        if (preg_match($reg, $time_hour_and_min) !== 1) {
            $msg = 'Bad format time. Should be something like "13:45" (hh:mm, 24 hour format).';
            throw new \RuntimeException($msg);
        }
    }
}
