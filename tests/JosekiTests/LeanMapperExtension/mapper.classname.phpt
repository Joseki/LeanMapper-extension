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



    function testGetTable()
    {
        Assert::same('user', $this->mapper->getTable('JosekiTests\\LeanMapperExtension\\Tables\\User'));
        Assert::same('user', $this->mapper->getTable('\\JosekiTests\\LeanMapperExtension\\Tables\\User'));
    }



    function testGetEntityClass()
    {
        Assert::same('JosekiTests\\LeanMapperExtension\\Tables\\User', $this->mapper->getEntityClass('user'));
    }



    function testGetTableByRepositoryClass()
    {
        Assert::same('user', $this->mapper->getTableByRepositoryClass('JosekiTests\\LeanMapperExtension\\Tables\\UserRepository'));
        Assert::same('user', $this->mapper->getTableByRepositoryClass('JosekiTests\\LeanMapperExtension\\Tables\\UserRepository'));
    }



    function testGetColumn()
    {
        Assert::same('pub_date', $this->mapper->getColumn('JosekiTests\\LeanMapperExtension\\Tables\\Book', 'pubDate'));
        Assert::same('author', $this->mapper->getColumn('JosekiTests\\LeanMapperExtension\\Tables\\Special\\Book', 'author'));
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
