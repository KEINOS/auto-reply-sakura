<?php
namespace KEINOS\Tests;

use \KEINOS\Tests\TestCase;

final class Construct_WeekdayToReplyTest extends TestCase
{
    public function testOnlyWeekday()
    {
        $weekday = 'Monday';

        $sample = new \KEINOS\AutoMailReply\WeekdayToReply($weekday);
        $this->assertTrue(\is_object($sample));
    }

    public function testWithWeekdayAndBegin()
    {
        $weekday    = 'Monday';
        $time_begin = '11:30';

        $sample = new \KEINOS\AutoMailReply\WeekdayToReply($weekday, $time_begin);
        $this->assertTrue(\is_object($sample));
    }

    public function testWithWeekdayAndBeginAndEnd()
    {
        $weekday    = 'Monday';
        $time_begin = '11:30';
        $time_end   = '13:00';

        $sample = new \KEINOS\AutoMailReply\WeekdayToReply($weekday, $time_begin, $time_end);
        $this->assertTrue(\is_object($sample));
    }

    public function testEndsBeforeBegin()
    {
        $weekday    = 'Monday';
        $time_begin = '13:30';
        $time_end   = '11:00';

        $this->expectException(\RuntimeException::class);
        $sample = new \KEINOS\AutoMailReply\WeekdayToReply($weekday, $time_begin, $time_end);
    }

}
