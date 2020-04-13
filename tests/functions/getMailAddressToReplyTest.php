<?php
namespace KEINOS\Tests;

use \KEINOS\Tests\TestCase;

final class Function_GetMailAddressToReplyTest extends TestCase
{
    // ========================================================================
    //  Tests
    // ========================================================================

    public function testReadSample1()
    {
        $name_file_dummy = 'sample1.mail';
        $path_dir_dummy  = $this->getPathDirDataDummy();
        $path_file_dummy = $path_dir_dummy . DIR_SEP . $name_file_dummy;
        $mail_received_dummy = \file_get_contents($path_file_dummy);

        $mail_actual = \KEINOS\AutoMailReply\getMailAddressToReply($mail_received_dummy);
        $mail_expect = 'foo@user-domain.com';
        $this->assertSame($mail_actual, $mail_expect, 'Did not return the expected email address.');
    }
}
