<?php
namespace KEINOS\Tests;

use \KEINOS\Tests\TestCase;

final class Function_IsTimeZoneAsiaTokyoTest extends TestCase
{
    // ========================================================================
    //  Methods (Tests follows)
    // ========================================================================

    public function getCodeSample($time_zone)
    {
        $code = <<< 'CODE'
        namespace KEINOS\AutoMailReply;
        date_default_timezone_set('%%__TIME_ZONE__%%');
        require_once('./src/constants.php');
        require_once('./src/functions.php');
        echo (isTimeZoneAsiaTokyo() === true) ? 'true' : 'false';
CODE;
        $code = \str_replace('%%__TIME_ZONE__%%', $time_zone, $code);
        $code = \str_replace(PHP_EOL, '', $code);

        return $code;
    }

    // ========================================================================
    //  Tests
    // ========================================================================

    public function testSetTimeZoneToTokyo()
    {
        $timezone = 'Asia/Tokyo';
        $code     = $this->getCodeSample($timezone);
        $command  = "/usr/local/bin/php -r \"${code}\"";

        // Run command externally
        $result_actual = $this->exec($command);

        $result_expect = 'true';
        $this->assertSame($result_actual, $result_expect, 'If the time zone is set to "Asia/Tokyo", it should return true.');
    }

    public function testSetTimeZoneNotTokyo()
    {
        $timezone = 'Europe/London';
        $code     = $this->getCodeSample($timezone);
        $command  = "/usr/local/bin/php -r \"${code}\"";

        // Run command externally
        $result_actual = $this->exec($command);

        $result_expect = 'false';
        $this->assertSame($result_actual, $result_expect, 'If the time zone is set other than "Asia/Tokyo", it should return false.');
    }
}
