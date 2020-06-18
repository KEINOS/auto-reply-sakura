<?php
namespace KEINOS\Tests;

use \KEINOS\Tests\TestCase;

final class Method_IsTimeInBetweenTest extends TestCase
{
    public function provideDataBeginEndExpect()
    {
        return [
            ['09:00', '',      '',      true],
            ['09:00', '',      '10:00', true],
            ['11:00', '',      '10:00', false],
            ['09:00', '10:00', '',      false],
            ['11:00', '10:00', '',      true],
            ['10:59', '11:00', '13:59', false],
            ['11:00', '11:00', '13:59', true],
            ['11:01', '11:00', '13:59', true],
            ['13:59', '11:00', '13:59', true],
            ['14:00', '11:00', '13:59', false],
            ['14:01', '11:00', '13:59', false],
        ];
    }

    /**
     * @dataProvider provideDataBeginEndExpect
     */
    public function testVariousTimeInputOfBegin($time_compare, $time_begin, $time_end, $expect)
    {
        $sample = new \KEINOS\AutoMailReply\WeekdayToReply('Monday', $time_begin, $time_end);
        $actual = $sample->isTimeInBetween($time_compare);

        $this->assertSame($expect, $actual);
    }

    public function testIntegerValue()
    {
        $time_begin = '11:00';
        $time_end   = '';
        $sample     = new \KEINOS\AutoMailReply\WeekdayToReply('Monday', $time_begin, $time_end);

        // Arg of method "isTimeInBetween()" should be in "H:i" time format string.
        $this->expectException(\RuntimeException::class);
        $time_compare = 1110;
        $actual = $sample->isTimeInBetween($time_compare);
    }

    public function testMalformedValue()
    {
        $time_begin = '11:00';
        $time_end   = '';
        $sample     = new \KEINOS\AutoMailReply\WeekdayToReply('Monday', $time_begin, $time_end);

        // Arg of method "isTimeInBetween()" should be in "H:i" time format string.
        $this->expectException(\RuntimeException::class);
        $time_compare = "1110";
        $actual = $sample->isTimeInBetween($time_compare);
    }

}
