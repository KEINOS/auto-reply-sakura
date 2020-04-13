<?php
namespace KEINOS\Tests;

use \KEINOS\Tests\TestCase;

final class Function_IsModeDebugTest extends TestCase
{
    // ========================================================================
    //  Methods (Tests follows)
    // ========================================================================

    public function getCodeSample()
    {
        $code = <<< 'CODE'
        namespace KEINOS\AutoMailReply;
        require_once('./src/constants.php');
        require_once('./src/functions.php');
        echo (isModeDebug() === true) ? 'true' : 'false';
CODE;
        $code = \str_replace(PHP_EOL, '', $code);

        return $code;
    }

    // ========================================================================
    //  Tests
    // ========================================================================

    public function testRegularReturn()
    {
        $bool_expect = (IS_MODE_DEBUG === true) ? true : false;
        $bool_actual = \KEINOS\AutoMailReply\isModeDebug();

        $this->assertSame($bool_expect, $bool_actual, 'Did not return the same bool of "IS_MODE_DEBUG" env variable.');
    }

    public function testSetEnvVarToTrue()
    {
        $code    = $this->getCodeSample();
        $command = "IS_MODE_DEBUG=true /usr/local/bin/php -r \"${code}\"";

        // Run command externally
        $result_array     = [];
        $return_var       = false;
        $result_last_line = \exec($command, $result_array, $return_var);
        $result_actual    = implode(PHP_EOL, $result_array);

        $result_expect = 'true';
        $this->assertSame($result_actual, $result_expect, 'If the env variable "IS_MODE_DEBUG" is set, it should return true.');
    }

    public function testSetEnvVarToFalse()
    {
        $code    = $this->getCodeSample();
        $command = "IS_MODE_DEBUG=false /usr/local/bin/php -r \"${code}\"";

        // Run command externally
        $result_array     = [];
        $return_var       = false;
        $result_last_line = \exec($command, $result_array, $return_var);
        $result_actual    = implode(PHP_EOL, $result_array);

        $result_expect  = 'false';
        $this->assertSame($result_actual, $result_expect, 'If the env variable "IS_MODE_DEBUG" is set as "false", it should return false.');
    }

    public function testSetEnvVarEmpty()
    {
        $code    = $this->getCodeSample();
        $command = "IS_MODE_DEBUG= /usr/local/bin/php -r \"${code}\"";

        // Run command externally
        $result_array     = [];
        $return_var       = false;
        $result_last_line = \exec($command, $result_array, $return_var);
        $result_actual    = implode(PHP_EOL, $result_array);

        $result_expect = 'false';
        $this->assertSame($result_actual, $result_expect, 'If the env variable "IS_MODE_DEBUG" is set but empty, it should return false.');
    }

    public function testSetEnvVarUnknown()
    {
        $code    = $this->getCodeSample();
        $command = "IS_MODE_DEBUG=foo /usr/local/bin/php -r \"${code}\"";

        // Run command externally
        $result_array     = [];
        $return_var       = false;
        $result_last_line = \exec($command, $result_array, $return_var);
        $result_actual    = implode(PHP_EOL, $result_array);

        $result_expect = 'false';
        $this->assertSame($result_actual, $result_expect, 'If the env variable "IS_MODE_DEBUG" is set other than "true", it should return false.');
    }
}
