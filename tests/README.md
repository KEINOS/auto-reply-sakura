# Test Files

Files in this directory are the tests to be used in PHPUnit.

- Each functions and class methods must have at least one test case in "../tests/".
- Making a test case first before implementing a function or method is preferred.

## Template of the test

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
2. Use and extend "\KEINOS\Tests\TestCase" class with the name "<FunctionName>Test" or "<MethodName>Test". (UpperCamel case and ending with "Test")
3. Test method's name must describe what to test. Beginning with "test". e.g.: `testIssue123()` `testGiveUnescapedData()`

### List of Assertions

- [Assertions](https://phpunit.readthedocs.io/en/latest/assertions.html) | PHPUnit @ ReadTheDocs.com

## How To Test

### For Non Docker Users

1. You need [PHP Composer](https://getcomposer.org/) installed. Run `composer --version` in your terminal to check if it's installed.
2. Move to the root of this repo and diagnose composer by: `composer diagnose`
3. Run the test by: `composer test`

### For Docker Users

If you have Docker installed then you don't need PHP to be installed in your environment.

For easy testing it is required to have docker-compose installed as well.

```bash
# Run tests over the container "test"
docker-compose run --rm test composer test
```

* NOTE: The first run takes time in order to create an image.
