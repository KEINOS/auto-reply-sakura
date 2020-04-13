# Configuration Files

To use the scripts in this repo, you need to:

1. Rename/Copy the file "config.json.sample" to "config.json".
2. Change/edit the value or delete un-necessary elements as it suits you.

* NOTE: Also you need to edit the templates. See "../template/" directory.

## File info

- sakura_php.ini:
  - "php.ini" file to include in Docker container. Same ini file with the Sakura-Internet standard plan's PHP5 ini file.
- sakura_debug_php.ini:
  - "php.ini" file to include in Docker container. Display errors are enabled.
- phpunit.xml:
  - XML file for unit test configuration. Use this file with PHPUnit's "--configuration" option.
