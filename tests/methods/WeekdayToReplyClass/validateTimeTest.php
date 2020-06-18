<?php
namespace KEINOS\Tests;

use \KEINOS\Tests\TestCase;

final class Method_ValidateTimeTest extends TestCase
{
    public function provideDataTimeAndAssert()
    {
        return [
            ['',       true],
            [null,     true],
            [1030,     false],
            [':',      false],
            ['11',     false],
            ['11:',    false],
            [':12',    false],
            ['0:0',    false],
            ['10:1',   false],
            ['00:00',  true],
            [' 1:00',  false],
            ['10:5 ',  false],
            ['01:00',  true],
            ['10:00',  true],
            ['10:05',  true],
            ['23:58',  true],
            ['24:00',  false],
            ['24:59',  false],
            ['-1:30',  false],
            ['25:00',  false],
            ['hh:mm',  false],
            ['100:00', false],
            ['3:00am', false],
            ['23:30:14', false],
            ['00:00:00', false]
        ];
    }

    /**
     * @dataProvider provideDataTimeAndAssert
     */
    public function testVariousTimeInputOfBegin($data, $expect)
    {
        $weekday = 'Monday';

        if ($expect === true) {
            $sample = new \KEINOS\AutoMailReply\WeekdayToReply($weekday, $data);
            $this->assertTrue(\is_object($sample));
        } else {
            $this->expectException(\RuntimeException::class);
            $sample = new \KEINOS\AutoMailReply\WeekdayToReply($weekday, $data);
        }
    }
}
