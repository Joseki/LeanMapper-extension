<?php

namespace JosekiTests\LeanMapperExtension;

use Joseki\LeanMapper\Mapper;
use Tester\Assert;
use Tester\TestCase;

$container = require __DIR__ . '/../bootstrap.php';

class MapperMock extends Mapper
{

    /** @var string */
    protected $defaultEntityNamespace = 'JosekiTests\LeanMapperExtension\Tables';

}

class MapperTest extends TestCase
{
    /** @var  MapperMock */
    private $mapper;



    function setUp()
    {
        $this->mapper = new MapperMock(['Special', 'camelCase']);
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

run(new MapperTest($container));
