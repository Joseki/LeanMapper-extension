<?php

use Tester\Assert;

$container = require __DIR__ . '/../bootstrap.php';

class UtilsTest extends Tester\TestCase
{

    public function testUtils()
    {
        Assert::same('camelCase', Joseki\LeanMapper\Utils::underscoreToCamel('camel_case'));
        Assert::same('camel_case', Joseki\LeanMapper\Utils::camelToUnderscore('camelCase'));
    }

}

run(new UtilsTest());
