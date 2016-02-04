<?php

namespace JosekiTests\LeanMapperExtension;

use Joseki\LeanMapper\Utils;
use Tester\Assert;
use Tester\TestCase;

$container = require __DIR__ . '/../bootstrap.php';

class UtilsTest extends TestCase
{

    public function testCamelToUnderscore()
    {
        Assert::same('camel_case', Utils::camelToUnderscore('camelCase'));
    }



    public function testUnderscoreToCamel()
    {
        Assert::same('camelCase', Utils::underscoreToCamel('camel_case'));
    }



    public function testTrimNamespace()
    {
        Assert::same('UtilsTest', Utils::trimNamespace('JosekiTests\LeanMapperExtension\UtilsTest'));
    }



    public function testExtractNamespace()
    {
        Assert::same('JosekiTests\LeanMapperExtension', Utils::extractNamespace('JosekiTests\LeanMapperExtension\UtilsTest'));
    }



    public function testTrimTableSchema()
    {
        Assert::same('table', Utils::trimTableSchema('schema.table'));
    }

}

run(new UtilsTest());
