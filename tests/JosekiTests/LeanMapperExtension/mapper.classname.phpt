<?php

use Tester\Assert;

$container = require __DIR__ . '/../bootstrap.php';
require __DIR__ . '/ServiceLocator.php';

class MapperTest extends Tester\TestCase
{
    /** @var  JosekiTests\LeanMapperExtension\MapperMock */
    private $mapper;



    function setUp()
    {
        $this->mapper = JosekiTests\LeanMapperExtension\ServiceLocator::getMapper();
    }



    public function testCamelToUnderscore()
    {
        Assert::same('camelCase', $this->mapper->underScoreToCamelMock('camel_case'));
    }



    public function testUnderscoreToCamel()
    {
        Assert::same('Special', $this->mapper->reformatNamespacePrefixMock('Special'));
        Assert::same('CamelCase', $this->mapper->reformatNamespacePrefixMock('camelCase'));
        Assert::same('UnderscoreSeparated', $this->mapper->reformatNamespacePrefixMock('underscore_separated'));
    }



    function testGetTable()
    {
        Assert::same('user', $this->mapper->getTable('JosekiTests\\LeanMapperExtension\\Tables\\User'));
        Assert::same('user', $this->mapper->getTable('\\JosekiTests\\LeanMapperExtension\\Tables\\User'));
        Assert::same('special_user', $this->mapper->getTable('JosekiTests\\LeanMapperExtension\\Tables\\Special\\User'));
        Assert::same('special_user', $this->mapper->getTable('\\JosekiTests\\LeanMapperExtension\\Tables\\Special\\User'));
    }



    function testGetEntityClass()
    {
        Assert::same('JosekiTests\\LeanMapperExtension\\Tables\\User', $this->mapper->getEntityClass('user'));
        Assert::same('JosekiTests\\LeanMapperExtension\\Tables\\Special\\User', $this->mapper->getEntityClass('special_user'));
        Assert::same('JosekiTests\\LeanMapperExtension\\Tables\\CamelCase\\User', $this->mapper->getEntityClass('camelcase_user'));
    }



    function testGetTableByRepositoryClass()
    {
        Assert::same('user', $this->mapper->getTableByRepositoryClass('JosekiTests\\LeanMapperExtension\\Tables\\UserRepository'));
        Assert::same(
            'special_user',
            $this->mapper->getTableByRepositoryClass('JosekiTests\\LeanMapperExtension\\Tables\\Special\\UserRepository')
        );
        Assert::same(
            'camel_case_user',
            $this->mapper->getTableByRepositoryClass('JosekiTests\\LeanMapperExtension\\Tables\\CamelCase\\UserRepository')
        );
    }



    function testGetColumn()
    {
        Assert::same('pub_date', $this->mapper->getColumn('JosekiTests\\LeanMapperExtension\\Tables\\Book', 'pubDate'));
    }



    function testGetEntityField()
    {
        Assert::same('pubDate', $this->mapper->getEntityField('book', 'pub_date'));
    }



    function testGetRelationshipColumn()
    {

    }
}

id(new MapperTest($container))->run();
