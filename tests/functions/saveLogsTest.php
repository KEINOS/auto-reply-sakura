<?php
namespace KEINOS\Tests;

use \KEINOS\Tests\TestCase;

final class Function_SaveLogsTest extends TestCase
{
    // ========================================================================
    //  Methods (Tests follows)
    // ========================================================================

    public function createCommandTest()
    {
        // Data for testing
        $name_file_log_dummy = $this->getNameFileLogDummy();
        $hash_sample         = $this->getHashSample();

        // Get sample code
        $code = $this->getCodeSample();
        // Replace string
        $code = \str_replace('%%__NAME_FILE_LOG_DUMMY__%%', $name_file_log_dummy, $code);
        $code = \str_replace('%%_DUMMY_MSG__%%', $hash_sample, $code);
        $code = \str_replace(PHP_EOL, '', $code);

        // Create command to run the code
        $command = "/usr/local/bin/php -r \"${code}\"";

        return $command;
    }

    public function getCodeSample()
    {
        // Define the log file name to dummy
        $code = <<< 'CODE'
        namespace KEINOS\AutoMailReply;

        const NAME_FILE_LOG='%%__NAME_FILE_LOG_DUMMY__%%';

        require_once('./src/constants.php');
        require_once('./src/functions.php');

        echo saveLogs('%%_DUMMY_MSG__%%') ? 'success' : 'fail';
CODE;

        return $code;
    }

    public function getNameFileLogDummy()
    {
        return 'dummy.log';
    }

    public function getPathFileLogDummy()
    {
        $name_file_log_dummy  = $this->getNameFileLogDummy();
        $path_dir_log_dummy   = $this->getPathDirRootScript();
        $path_file_log_dummy  = $path_dir_log_dummy . DIRECTORY_SEPARATOR . $name_file_log_dummy;

        return $path_file_log_dummy;
    }

    public function getHashSample()
    {
        static $hash;
        if (isset($hash)) {
            return $hash;
        }
        $hash = \hash('md5', time());

        return $hash;
    }

    public function removeFileLogDummy()
    {
        $path_file_log_dummy = $this->getPathFileLogDummy();
        if (\file_exists($path_file_log_dummy)) {
            if (\unlink($path_file_log_dummy) === false) {
                throw new \RuntimeException('Failed to unlink existing dummy log at: ' . $path_file_log_dummy);
            }
        }
    }

    public function setUp()
    {
        // Pre-processing before each test method
        $this->removeFileLogDummy();
    }

    public function tearDown()
    {
        // Post-processing after each test method
        $this->removeFileLogDummy();
    }

    // ========================================================================
    //  Tests
    // ========================================================================

    public function testSimpleSave()
    {
        try {
            $path_file_log_dummy = $this->getPathFileLogDummy();

            // Run command externally
            $command = $this->createCommandTest();
            $result_save = $this->exec($command);
            if ($result_save !== 'success') {
                throw new \RuntimeException('Failed to save the dummy log at: ' . $path_file_log_dummy);
            }

            // Get contents of the saved log
            $contents_actual = \file_get_contents($path_file_log_dummy);
            $contents_expect = $this->getHashSample() . PHP_EOL;

            // Assertion
            $this->assertSame($contents_actual, $contents_expect, "ACTUAL: ${contents_actual}");
        } catch (\RuntimeException $e) {
            // Get error
            $msg_error = $e->getMessage() . PHP_EOL;
            throw new \RuntimeException("Failed to execute the sample code in the test. \n  Error Msg: " . trim($msg_error));
        }
    }

    public function testAppendToExistingLog()
    {
        $path_file_log_dummy = $this->getPathFileLogDummy();
        $contents_expect     = '';
        $num_repeat_logging  = \mt_rand(2, 10);

        $command = $this->createCommandTest();
        for ($i = 0; $i < $num_repeat_logging; $i++) {
            // Run command externally to add/append log
            $result_save = $this->exec($command);
            if ($result_save !== 'success') {
                throw new \RuntimeException('Failed to save the dummy log at: ' . $path_file_log_dummy);
            }
            $contents_expect .= $this->getHashSample() . PHP_EOL;
        }

        // Get contents of the saved log
        $contents_actual = \file_get_contents($path_file_log_dummy);

        // Assertion
        $this->assertSame($contents_actual, $contents_expect, 'Fail to append log.');
    }
}
