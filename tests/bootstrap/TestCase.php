<?php

namespace KEINOS\Tests;

defined('DIR_SEP')   or define('DIR_SEP', DIRECTORY_SEPARATOR);

abstract class TestCase extends \PHPUnit\Framework\TestCase
{
    public function exec($command, &$return_var = false)
    {
        try {
            // Initialize
            $result_array = [];

            // Execute command
            $result_last_line = \exec($command, $result_array, $return_var);
            $result = implode(PHP_EOL, $result_array);

            if ($return_var !== 0) {
                throw new \RuntimeException('Exec fail with non zero status. Returned result: ' . PHP_EOL . $result);
            }
            return $result;
        } catch (\RuntimeException $e) {
            // Get error
            $msg_error = $e->getMessage() . PHP_EOL;
            throw new \RuntimeException("Failed to execute the sample code in the test. \n  Error Msg: " . trim($msg_error));
        }
    }

    public function getPathDirDataDummy()
    {
        $path_dir_root_script = $this->getPathDirRootScript();

        return $path_dir_root_script . DIR_SEP . NAME_DIR_TEST . DIR_SEP . NAME_DIR_MAIL_DUMMY;
    }

    public function getPathDirRootScript()
    {
        static $path_dir_script;
        if (isset($path_dir_script)) {
            return $path_dir_script;
        }
        $name_file_main  = 'auto-reply.php';

        $path_dir_parent = \dirname(\dirname(__DIR__));
        $path_file_main  = $path_dir_parent . DIRECTORY_SEPARATOR . $name_file_main;
        if (! \file_exists($path_file_main)) {
            throw new \RuntimeException('Mal-placed directory structure. PATH: ' . $path_file_main);
        }
        $path_dir_script = $path_dir_parent;

        return $path_dir_script;
    }

    public function getPathDirTemplate()
    {
        $path_dir_root_script = $this->getPathDirRootScript();

        return $path_dir_root_script . DIR_SEP . NAME_DIR_TEMPLATE;
    }
}
