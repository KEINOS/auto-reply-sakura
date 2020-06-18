<?php

/**
 * Sample usage of ConfigInfo class
 */

require_once(__DIR__ . '/../vendor/autoload.php');

$path_dir_sample  = __DIR__ .  '/../tests/data_dummy/';
$name_file_sample = 'config.sample1_regular.json';
$path_file_sample = "${path_dir_sample}/${name_file_sample}";

$obj_conf = new KEINOS\AutoMailReply\ConfigInfo($path_file_sample);

print_r($obj_conf);
