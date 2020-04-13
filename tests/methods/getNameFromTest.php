<?php
namespace KEINOS\Tests;

use \KEINOS\Tests\TestCase;

final class Method_GetNameFromTest extends TestCase
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

        $result_actual = $obj->getNameFrom();
        $result_expect = 'My Sample Name';
        $this->assertSame($result_actual, $result_expect, 'Did not return the same value from config file.');
    }
}
