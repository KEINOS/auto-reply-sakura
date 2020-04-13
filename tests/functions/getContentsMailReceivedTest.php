<?php
namespace KEINOS\Tests;

use \KEINOS\Tests\TestCase;

final class Function_GetContentsMailReceivedTest extends TestCase
{
    // ========================================================================
    //  Tests
    // ========================================================================

    public function testReadFromStdin()
    {
        $hash_sample = hash('md5', time() . __FILE__);

        $code = <<< 'CODE'
        namespace KEINOS\AutoMailReply;
        require_once('./src/functions.php');
        echo getContentsFromStdin();
CODE;
        $code = \str_replace(PHP_EOL, '', $code);

        // Pipe the hash value as a input sample to script's STDIN
        $command = "/bin/echo '${hash_sample}' | /usr/local/bin/php -r \"${code}\"";

        // Run command externally
        $contents_actual = $this->exec($command);

        $contents_expect = $hash_sample;
        $this->assertSame($contents_actual, $contents_expect, 'Did not return the same content from STDIN given to the script.');
    }
}
