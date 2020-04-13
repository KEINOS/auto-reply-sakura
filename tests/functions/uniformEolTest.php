<?php

namespace KEINOS\Tests;

use \KEINOS\Tests\TestCase;

final class Function_UniformEOLTest extends TestCase
{
    // ========================================================================
    //  Tests
    // ========================================================================
    public function testDefault()
    {
        $this->assertSame(PHP_EOL, \KEINOS\AutoMailReply\uniformEOL("\n"));
        $this->assertSame(PHP_EOL, \KEINOS\AutoMailReply\uniformEOL("\r"));
        $this->assertSame(PHP_EOL, \KEINOS\AutoMailReply\uniformEOL("\r\n"));
    }

    public function testLF()
    {
        $this->assertSame("\n", \KEINOS\AutoMailReply\uniformEOL("\n", "\n"));
        $this->assertSame("\n", \KEINOS\AutoMailReply\uniformEOL("\r", "\n"));
        $this->assertSame("\n", \KEINOS\AutoMailReply\uniformEOL("\r\n", "\n"));
    }

    public function testCR()
    {
        $this->assertSame("\r", \KEINOS\AutoMailReply\uniformEOL("\n", "\r"));
        $this->assertSame("\r", \KEINOS\AutoMailReply\uniformEOL("\r", "\r"));
        $this->assertSame("\r", \KEINOS\AutoMailReply\uniformEOL("\r\n", "\r"));
    }

    public function testCRLF()
    {
        $this->assertSame("\r\n", \KEINOS\AutoMailReply\uniformEOL("\n", "\r\n"));
        $this->assertSame("\r\n", \KEINOS\AutoMailReply\uniformEOL("\r", "\r\n"));
        $this->assertSame("\r\n", \KEINOS\AutoMailReply\uniformEOL("\r\n", "\r\n"));
    }
}
