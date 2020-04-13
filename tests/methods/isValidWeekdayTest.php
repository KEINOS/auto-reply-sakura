<?php
namespace KEINOS\Tests;

use \KEINOS\Tests\TestCase;

final class Method_IsValidWeekdayTest extends TestCase
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

    public function testValid()
    {
        $array_valid = [
            'Sunday',
            'Monday',
            'Tuesday',
            'Wednesday',
            'Thursday',
            'Friday',
            'Saturday',
        ];
        foreach($array_valid as $weekday){
            $result = $this->obj->isValidWeekday($weekday);
            $this->assertTrue($result, "\"${weekday}\" is not a valid weekday.");
        }
    }

    public function testInValid()
    {
        $array_valid = [
            'Sun',
            'Mon',
            'Tue',
            'Wed',
            'Thu',
            'Fri',
            'Sat',
            'foo',
        ];
        foreach($array_valid as $weekday){
            $result = $this->obj->isValidWeekday($weekday);
            $this->assertFalse($result, "\"${weekday}\" should not be a valid weekday.");
        }
    }
}
