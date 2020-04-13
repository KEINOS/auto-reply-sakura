<?php
namespace KEINOS\Tests;

use \KEINOS\Tests\TestCase;

final class Function_GetMailDummyTest extends TestCase
{
    // ========================================================================
    //  Methods (Tests follows)
    // ========================================================================

    public function getPathDirSample()
    {
        $path_dir_root_script = $this->getPathRootScript();
        $path_dir_sample      = $path_dir_root_script . DIR_SEP . NAME_DIR_TEST . DIR_SEP . NAME_DIR_MAIL_DUMMY;

        return $path_dir_sample;
    }

    // ========================================================================
    //  Tests
    // ========================================================================

    public function testReadSampleDefault()
    {
        $name_file_sample = NAME_FILE_MAIL_DUMMY;
        $path_dir_sample  = $this->getPathDirDataDummy();
        $path_file_sample = $path_dir_sample . DIR_SEP . $name_file_sample;

        $body_expect = \file_get_contents($path_file_sample);
        $body_actual = \KEINOS\AutoMailReply\getMailDummy(); // Default should be 'sample1.mail'
        $this->assertSame($body_expect, $body_actual, 'By default, dummy file should be read from: ' . $path_file_sample);
    }

    public function testReadSampleDesignated()
    {
        $name_file_sample = 'sample2.mail';

        $path_dir_sample  = $this->getPathDirDataDummy();
        $path_file_sample = $path_dir_sample . DIR_SEP . $name_file_sample;

        $body_expect = \file_get_contents($path_file_sample);
        $body_actual = \KEINOS\AutoMailReply\getMailDummy($name_file_sample);
        $this->assertSame($body_expect, $body_actual, 'Does not read the given file name from sample dir. File path: ' . $path_file_sample);
    }

    public function testSpecifyUnExistingFile()
    {
        $this->expectException(\RuntimeException::class);
        $results = \KEINOS\AutoMailReply\getMailDummy('foobar');
    }
}
