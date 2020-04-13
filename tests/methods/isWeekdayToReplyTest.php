<?php
namespace KEINOS\Tests;

use \KEINOS\Tests\TestCase;

final class Method_IsWeekdayToReplyTest extends TestCase
{
    // ========================================================================
    //  Method (Test follows)
    // ========================================================================
    public $obj;
    public function setUp()
    {
        // Sample config file path
        $name_file_config = 'config.sample1_regular.json';
        $path_dir_dummy   = $this->getPathDirDataDummy();
        $path_file_config = $path_dir_dummy . DIR_SEP . $name_file_config;

        $this->obj = new \KEINOS\AutoMailReply\ConfigInfo($path_file_config);
    }

    // ========================================================================
    //  Tests
    // ========================================================================

    public function testValidTimeStamp()
    {
        // Monday, Tuesday
        $array = [
            '2020/04/13', // Monday
            '2020/04/14', // Tuesday
        ];
        foreach ($array as $sample) {
            $timestamp = \strtotime($sample);
            $result = $this->obj->isWeekdayToReply($timestamp);
            $this->assertTrue($result, "\"${sample}\" is not a date for matching weekday.");
        }
    }

    public function testInValidDate()
    {
        // Other than Monday, Tuesday
        $array = [
            '2020/04/11', // Saturday
            '2020/04/12', // Sunday
            // '2020/04/13', // Monday
            // '2020/04/14', // Tuesday
            '2020/04/15', // Wednesday
            '2020/04/16', // Thursday
            '2020/04/17', // Friday
        ];
        foreach ($array as $sample) {
            $timestamp = \strtotime($sample);
            $result = $this->obj->isWeekdayToReply($timestamp);
            $this->assertFalse($result, "\"${sample}\" should return false.");
        }
    }

    public function testInValidTimestamp()
    {
        $dummy_sample = 'Not Good';
        $result = $this->obj->isWeekdayToReply($dummy_sample);
        $this->assertFalse($result, "Invalid timestamp \"${dummy_sample}\" should return as false.");
    }
}
