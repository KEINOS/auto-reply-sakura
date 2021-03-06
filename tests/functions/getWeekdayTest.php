<?php
namespace KEINOS\Tests;

use \KEINOS\Tests\TestCase;

final class Function_GetWeekdayTest extends TestCase
{
    // ========================================================================
    //  Tests
    // ========================================================================

    public function testTimeStampAsInteger()
    {
        $timestamp = 1586441962;
        $expect = 'Thursday';
        $actual = \KEINOS\AutoMailReply\getWeekday($timestamp);

        $this->assertSame($expect, $actual);
    }

    public function testTimeStampAsString()
    {
        $timestamp = "1586441962";
        $expect = 'Thursday';
        $actual = \KEINOS\AutoMailReply\getWeekday($timestamp);

        $this->assertSame($expect, $actual);
    }
}
