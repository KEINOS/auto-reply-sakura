<?php
namespace KEINOS\Tests;

use \KEINOS\Tests\TestCase;

final class Function_GetContentsFromTest extends TestCase
{
    // ========================================================================
    //  Tests
    // ========================================================================

    public function testReadExistingFile()
    {
        $path_dir_dummy  = $this->getPathDirDataDummy();
        $path_file_dummy = $path_dir_dummy . DIR_SEP . NAME_FILE_MAIL_DUMMY;

        $contents_expect = \file_get_contents($path_file_dummy);
        $contents_actual = \KEINOS\AutoMailReply\getContentsFrom($path_file_dummy);
        $this->assertSame($contents_expect, $contents_actual, 'Did not return the specified file: ' . $path_file_dummy);
    }

    public function testReadNonExistingFile()
    {
        $this->expectException(\RuntimeException::class);
        $contents_actual = \KEINOS\AutoMailReply\getContentsFrom('dummy');
    }

    public function testReadDataFromStdin()
    {
        $hash_sample = hash('md5', time() . __FILE__);

        $code = <<< 'CODE'
        namespace KEINOS\AutoMailReply;
        require_once('./src/functions.php');
        echo getContentsFrom('php://stdin');
CODE;
        $code    = \str_replace(PHP_EOL, '', $code);

        // Pipe the hash value as a input sample to script's STDIN
        $command = "/bin/echo '${hash_sample}' | /usr/local/bin/php -r \"${code}\"";

        $contents_actual = $this->exec($command);
        $contents_expect = $hash_sample;
        $this->assertSame($contents_expect, $contents_actual, 'Did not return the same content from STDIN given to the script.');
    }
}
