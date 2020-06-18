<?php
namespace KEINOS\Tests;

use \KEINOS\Tests\TestCase;

final class Method_ValidateWeekdayTest extends TestCase
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
    }

    public function testIrregularUsage()
    {
        // Sample config file path
        $name_file_config = 'config.sample5_wrong_weekday.json';
        $path_dir_dummy   = $this->getPathDirDataDummy();
        $path_file_config = $path_dir_dummy . DIR_SEP . $name_file_config;

        // It should throw exception when creating the object
        $this->expectException(\RuntimeException::class);
        $obj = new \KEINOS\AutoMailReply\ConfigInfo($path_file_config);
        //$result = $obj->validateWeekday();
    }
}
