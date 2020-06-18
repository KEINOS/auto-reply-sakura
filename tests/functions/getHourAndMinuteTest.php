<?php
namespace KEINOS\Tests;

use \KEINOS\Tests\TestCase;

final class Function_GetHourAndMinuteTest extends TestCase
{
    // ========================================================================
    //  Tests
    // ========================================================================

    public function testTimeStampAsInteger()
    {
        $time_stamp = 1602320460;

        $expect = date('H:i', $time_stamp);
        $actual = \KEINOS\AutoMailReply\getHourAndMinute($time_stamp);

        $this->assertSame($expect, $actual);
    }

    public function testTimeStampAsString()
    {
        $time_stamp = "1602320460";

        $expect = date('H:i', $time_stamp);
        $actual = \KEINOS\AutoMailReply\getHourAndMinute($time_stamp);

        $this->assertSame($expect, $actual);
    }
}
