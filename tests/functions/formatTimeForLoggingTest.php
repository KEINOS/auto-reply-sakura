<?php
namespace KEINOS\Tests;

use \KEINOS\Tests\TestCase;

final class Function_FormatTimeToLogTest extends TestCase
{
    // ========================================================================
    //  Tests
    // ========================================================================

    public function testFormat()
    {
        $time = 1586358443;
        $result_expect = '2020/04/09 00:07 (Thursday):Asia/Tokyo';

        $result_actual = \KEINOS\AutoMailReply\formatTimeForLogging($time);
        $this->assertSame($result_expect, $result_actual, 'Malformed format. Must be in "Y/m/d H:i (l):e" format. Ex: ' . $result_expect);

        $result_dummy = \KEINOS\AutoMailReply\formatTimeForLogging($time + 60);
        $this->assertNotSame($result_expect, $result_dummy, 'Time does not move with different time stamp.');
    }
}
