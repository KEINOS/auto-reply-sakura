<?php
namespace KEINOS\Tests;

use \KEINOS\Tests\TestCase;

final class Function_GetCurrentTimeTest extends TestCase
{
    // ========================================================================
    //  Tests
    // ========================================================================

    public function testIfTheValueIsSameAsConstantValue()
    {
        $time_actual = \KEINOS\AutoMailReply\getCurrentTime();
        $time_expect = TIME_CURRENT;
        $this->assertSame($time_actual, $time_expect, 'The return value must be equal to constant "TIME_CURRENT"');
    }

    public function testDoNotBeEmpty()
    {
        $time_stamp = \KEINOS\AutoMailReply\getCurrentTime();
        $this->assertFalse(empty($time_stamp), 'The return value must not be empty.');
    }

}
