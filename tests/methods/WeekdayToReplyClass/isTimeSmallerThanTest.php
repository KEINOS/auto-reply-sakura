<?php
namespace KEINOS\Tests;

use \KEINOS\Tests\TestCase;

final class Method_IsTimeSmallerThanTest extends TestCase
{
    public function provideDataBeginEndExpect()
    {
        return [
            ['00:00', '23:59', true],
            ['00:01', '00:02', true],
            ['11:00', '11:01', true],
            ['13:00', '13:01', true],
            ['11:00', '11:00', false],
            ['13:00', '13:00', false],
            ['13:00', '12:59', false],
            ['11:30', '11:00', false],
        ];
    }

    /**
     * @dataProvider provideDataBeginEndExpect
     */
    public function testVariousTimeInputOfBegin($time_begin, $time_end, $expect)
    {
        $sample = new \KEINOS\AutoMailReply\WeekdayToReply('Monday');
        $actual = $sample->isTimeSmallerThan($time_begin, $time_end);

        $this->assertSame($actual, $expect);
    }

    /**
     * "isTimeSmallerThan()" method should not assume time
     */
    public function testEmptyBeginValue()
    {
        $sample = new \KEINOS\AutoMailReply\WeekdayToReply('Monday');
        $this->expectException(\RuntimeException::class);
        $actual = $sample->isTimeSmallerThan('', '11:30');
    }

    /**
     * "isTimeSmallerThan()" method should not assume time
     */
    public function testEmptyEndValue()
    {
        $sample = new \KEINOS\AutoMailReply\WeekdayToReply('Monday');
        $this->expectException(\RuntimeException::class);
        $actual = $sample->isTimeSmallerThan('11:30', '');
    }
}
