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
        Assert::same('user', $this->mapper->getTable('Tables\\User'));
        Assert::same('user', $this->mapper->getTable('\\Tables\\User'));
    }



    function testGetEntityClass()
    {
        Assert::same('Tables\\User', $this->mapper->getEntityClass('user'));
    }



    function testGetTableByRepositoryClass()
    {
        Assert::same('user', $this->mapper->getTableByRepositoryClass('Tables\\UserRepository'));
        Assert::same('user', $this->mapper->getTableByRepositoryClass('Tables\\UserRepository'));
    }



    function testGetColumn()
    {
        Assert::same('pub_date', $this->mapper->getColumn('Tables\\Book', 'pubDate'));
        Assert::same('author', $this->mapper->getColumn('Tables\\Special\\Book', 'author'));
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
