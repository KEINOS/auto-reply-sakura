<?php
namespace KEINOS\Tests;

use \KEINOS\Tests\TestCase;

final class Function_GetWeekdayTest extends TestCase
{
    // ========================================================================
    //  Tests
    // ========================================================================

    public function testReadSample1()
    {
        $timestamp      = 1586441962;
        $weekday_expect = 'Thursday';
        $weekday_actual = \KEINOS\AutoMailReply\getWeekday($timestamp);

        $this->assertSame($weekday_expect, $weekday_actual, $weekday_actual . ' ' . $timestamp);
    }

    public function testTimeStampAsString()
    {
        $timestamp      = "1586441962";
        $weekday_expect = 'Thursday';
        $weekday_actual = \KEINOS\AutoMailReply\getWeekday($timestamp);

        $this->assertSame($weekday_expect, $weekday_actual, $weekday_actual . ' ' . $timestamp);
    }
}
