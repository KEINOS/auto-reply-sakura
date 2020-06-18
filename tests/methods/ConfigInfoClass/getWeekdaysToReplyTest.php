<?php
namespace KEINOS\Tests;

use \KEINOS\Tests\TestCase;

final class Method_GetWeekdaysToReplyTest extends TestCase
{
    // ========================================================================
    //  Tests
    // ========================================================================

    public function testRegularUsage()
    {
        // Sample config file path
        $name_file_config = 'config.sample1_regular.json';
        $path_dir_dummy   = $this->getPathDirDataDummy();
        $path_file_config = $path_dir_dummy . DIR_SEP . $name_file_config;

        $obj = new \KEINOS\AutoMailReply\ConfigInfo($path_file_config);

        $result_actual = $obj->getWeekdaysToReply();
        $result_expect = [
            'Monday',
            'Tuesday',
        ];
        $this->assertSame($result_actual, $result_expect, 'Did not return the same value from config file.');
    }

    public function testIfWeekdayIsMissing()
    {
        // Sample config file with no weekday set
        $name_file_config = 'config.sample3_no_weekday.json';
        $path_dir_dummy   = $this->getPathDirDataDummy();
        // Sample config file path
        $path_file_config = $path_dir_dummy . DIR_SEP . $name_file_config;

        $obj = new \KEINOS\AutoMailReply\ConfigInfo($path_file_config);

        $result_actual = $obj->getWeekdaysToReply();
        $result_expect = [];
        $this->assertSame($result_actual, $result_expect, 'Did not return the same value from config file.');
    }

    public function testIfWeekdayIsString()
    {
        // Sample config file path
        $name_file_config = 'config.sample4_str_weekday.json';
        $path_dir_dummy   = $this->getPathDirDataDummy();
        $path_file_config = $path_dir_dummy . DIR_SEP . $name_file_config;

        $this->expectException(\RuntimeException::class);
        $obj = new \KEINOS\AutoMailReply\ConfigInfo($path_file_config);
    }
}
