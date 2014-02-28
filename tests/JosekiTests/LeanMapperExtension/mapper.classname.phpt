<?php

use JosekiTests\Mapper\TestMapper;
use Tester\Assert;

$container = require __DIR__ . '/../bootstrap.php';
require __DIR__ . '/ServiceLocator.php';

class MapperTest extends Tester\TestCase
{
	/** @var  TestMapper */
	private $mapper;


	function setUp()
	{
		/** @var TestMapper $mapper */
		$this->mapper = \Joseki\Tests\ServiceLocator::getMapper();
	}

	function testGetTable()
	{
		Assert::same('article', $this->mapper->getTable('JosekiTests\\Tables\\Article'));
		Assert::same('article', $this->mapper->getTable('\\JosekiTests\\Tables\\Article'));
		Assert::same('modul_entity', $this->mapper->getTable('JosekiTests\\Tables\\Modul\\Entity'));
		Assert::same('modul_entity', $this->mapper->getTable('\\JosekiTests\\Tables\\Modul\\Entity'));
	}

	function testGetEntityClass() {
		Assert::same('JosekiTests\\Tables\\Article', $this->mapper->getEntityClass('article'));
		Assert::same('JosekiTests\\Tables\\Modul\\Entity', $this->mapper->getEntityClass('modul_entity'));
	}

	function testGetColumn() {
		Assert::same('long_name', $this->mapper->getColumn('JosekiTests\\Tables\\Article', 'longName'));
	}

	function testGetEntityField() {
		Assert::same('longName', $this->mapper->getEntityField('article', 'long_name'));
	}

	function testGetRelationshipColumn() {

	}

	function testGetTableByRepositoryClass() {
		Assert::same('article', $this->mapper->getTableByRepositoryClass('JosekiTests\\Tables\\ArticleRepository'));
		Assert::same('modul_entity', $this->mapper->getTableByRepositoryClass('JosekiTests\\Tables\\Modul\\EntityRepository'));
	}
}

id(new MapperTest($container))->run();
