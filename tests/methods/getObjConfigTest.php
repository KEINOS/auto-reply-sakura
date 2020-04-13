<?php
namespace KEINOS\Tests;

use \KEINOS\Tests\TestCase;

final class Method_GetObjConfigTest extends TestCase
{
    // ========================================================================
    //  Methods (Tests follows)
    // ========================================================================

    public function getPathFileConfig($name_file_config)
    {
        $path_dir_dummy   = $this->getPathDirDataDummy();
        $path_file_config = $path_dir_dummy . DIR_SEP . $name_file_config;

        return $path_file_config;
    }

    // ========================================================================
    //  Tests
    // ========================================================================

    public function testPrivateMethod()
    {
        $name_file_config = 'config.sample1_regular.json';
        $path_file_config = $this->getPathFileConfig($name_file_config);

        // Create object
        $obj = new \KEINOS\AutoMailReply\ConfigInfo($path_file_config);
        // Reflect the object to test private method
        $reflection = new \ReflectionClass($obj);
        // Set private method name for testing
        $method = $reflection->getMethod('getObjConfig');
        // Let it be accessible
        $method->setAccessible(true);

        $result_actual = $method->invoke($obj, $path_file_config);
        $result_expect = \json_decode(\file_get_contents($path_file_config));
        $this->assertEquals($result_actual, $result_expect, 'Did not return the same JSON object from the config file.');
    }

    public function testGiveMalformedFile()
    {
        $name_file_config = 'config.sample2_malformed.json';
        $path_file_config = $this->getPathFileConfig($name_file_config);

        $this->expectException(\RuntimeException::class);
        $obj = new \KEINOS\AutoMailReply\ConfigInfo($name_file_config);
    }

    public function testSpecifyUnExistingFile()
    {
        $this->expectException(\RuntimeException::class);
        $obj = new \KEINOS\AutoMailReply\ConfigInfo('foobar');
    }
}
