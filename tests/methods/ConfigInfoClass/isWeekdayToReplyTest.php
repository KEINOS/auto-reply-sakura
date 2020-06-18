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
        //   Reply date:
        //     "weekday": Monday,  "begin": "18:00" ("end": "23:59")
        //     "weekday": Tuesday, (whole day)
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
            '2020/04/13 18:00', // Monday
            '2020/04/14 18:00', // Tuesday
        ];

        foreach ($array as $sample) {
            $timestamp = \strtotime($sample);
            $result = $this->obj->isWeekdayToReply($timestamp);
            $this->assertTrue($result, "\"${sample}\" is not a date for matching weekday.");
        }
    }

    public function testInValidDate()
    {
        // Other than Tuesday and before 18:00 of Monday
        $array = [
            '2020/04/11', // Saturday
            '2020/04/12', // Sunday
            '2020/04/13 17:00', // Monday
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

        $this->expectException(\RuntimeException::class);
        $result = $this->obj->isWeekdayToReply($dummy_sample);
    }
}
