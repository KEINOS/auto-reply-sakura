<?php
namespace KEINOS\Tests;

use \KEINOS\Tests\TestCase;

final class Function_GetContentsFromStdinTest extends TestCase
{
    // ========================================================================
    //  Tests
    // ========================================================================

    public function testReadFromStdin()
    {
        $contents_expect = hash('md5', time());
        $code = <<< 'CODE'
        namespace KEINOS\AutoMailReply;
        require_once('./src/functions.php');
        echo getContentsFromStdin();
CODE;
        $code    = \str_replace(PHP_EOL, '', $code);
        $command = "/bin/echo '${contents_expect}' | /usr/local/bin/php -r \"${code}\"";

        // Run command externally
        $contents_actual = $this->exec($command);

        $this->assertSame($contents_expect, $contents_actual, 'Did not return the same content from STDIN given to the script.');
    }
}
