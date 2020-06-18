<?php
namespace KEINOS\Tests;

use \KEINOS\Tests\TestCase;

final class Method_IsValidWeekdayTest extends TestCase
{
    public function provideDataWeekdayOk()
    {
        return [
            ['Sunday'],
            ['Monday'],
            ['Tuesday'],
            ['Wednesday'],
            ['Thursday'],
            ['Friday'],
            ['Saturday'],
        ];
    }

    public function provideDataWeekdayNg()
    {
        return [
            ['Sun'],
            ['Mon'],
            ['Tue'],
            ['Wed'],
            ['Thu'],
            ['Fri'],
            ['Sat'],
            ['sunday'],
            ['Momday'],
            [1],
            ['1']
        ];
    }

    /**
     * @dataProvider provideDataWeekdayOk
     */
    public function testRegularWeekdayInput($weekday)
    {
        $sample = new \KEINOS\AutoMailReply\WeekdayToReply($weekday);

        $this->assertTrue(\is_object($sample));
    }

    /**
     * @dataProvider provideDataWeekdayNg
     */
    public function testIrregularWeekdayInput($weekday)
    {
        $this->expectException(\RuntimeException::class);
        $sample = new \KEINOS\AutoMailReply\WeekdayToReply($weekday);
    }

}
