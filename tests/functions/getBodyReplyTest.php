<?php
namespace KEINOS\Tests;

use \KEINOS\Tests\TestCase;

final class Function_GetBodyReplyTest extends TestCase
{
    // ========================================================================
    //  Tests
    // ========================================================================

    public function testReadUtf8()
    {
        $name_file_body_reply = 'reply_body.utf8.txt.sample';

        $path_dir_template    = $this->getPathDirTemplate();
        $path_file_body_reply = $path_dir_template. DIR_SEP . $name_file_body_reply;
        $body_expect = \file_get_contents($path_file_body_reply);
        $body_expect = PHP_EOL . trim($body_expect);

        $body_actual = \KEINOS\AutoMailReply\getBodyReply($name_file_body_reply);
        $this->assertSame($body_actual, $body_expect, 'Does not read from template directory.');
    }
}
