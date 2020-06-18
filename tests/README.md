# Test Files

This directory contains unit test files of PHPUnit.

## How To Run The Tests

These tests were designed on the assumption that below apps/commands were installed locally.

- Docker
- docker-compose
- PHP (No PHP version asked)
- Composer

Due to the need of SMTP server to check if the replied email was sent properly, Docker and docker-compose were used.

```shellsession
$ # Build both SMTP and PHP5 containers
$ docker-compose build
...
$
$ # Launch SMTP container then PHP5 container and connects the tty to the PHP5 container.
$ composer docker-dev
...
/app # # Check PHP version of the container
/app # php -v
PHP 5.6.40 (cli) (built: Jan 31 2019 01:25:07)
Copyright (c) 1997-2016 The PHP Group
Zend Engine v2.6.0, Copyright (c) 1998-2016 Zend Technologies
/app #
/app # # Run tests inside the PHP5 container
/app # composer test
...
All tests passed.
/app #
/app # # Exit from PHP5 container and shuts down both SMTP and PHP5 container
/app # exit
...
$
```

## How To Write Tests

- Each functions and class methods must have at least one test case in "../tests/".
- Making a test case first before implementing a function or method is preferred.

### Template of the test

```php
<?php
namespace KEINOS\AutoMailReply;

use \KEINOS\Tests\TestCase;

final class SayHelloTest extends TestCase
{
    public function testGiveRightData()
    {
        $this->assertSame('Hello, World!', sayHello('World'));
    }

    public function testGiveWrongData()
    {
        $this->assertNotSame('Hello, World!', sayHello('Kitty'));
    }
}
```

1. File name must be "<functionName>Test.php" or "<methodName>Test.php" format. (lowerCamel case and ending with "Test")
2. Test class must be extended from "\KEINOS\Tests\TestCase" class with the name "<FunctionName>Test" or "<MethodName>Test". (UpperCamel case and ending with "Test")
3. Test method's name must describe what to test. Beginning with "test" in lowercase. e.g.: `testIssue123()` `testGiveUnescapedData()`

### List of Assertions

- [Assertions](https://phpunit.readthedocs.io/en/latest/assertions.html) | PHPUnit @ ReadTheDocs.com
