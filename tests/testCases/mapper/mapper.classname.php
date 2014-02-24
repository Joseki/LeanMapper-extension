<?php

use Joseki\LeanMapper\Mapper;
use Nette\DI\Container;
use Tester\Assert;

$container = require __DIR__ . '/../bootstrap.php';

class MapperTest extends Tester\TestCase
{
	private $container;
	/** @var  Joseki\LeanMapper\Mapper */
	private $mapper;

	function __construct(Container $container)
	{
		$this->container = $container;
	}

	function setUp()
	{
		/** @var Joseki\LeanMapper\Mapper $mapper */
		$this->mapper = $this->container->getByType('\\Joseki\\LeanMapper\\Mapper');
		$this->mapper->addTablePrefix('Fakturace');
	}

	function testGetTable()
	{
		Assert::same('user', $this->mapper->getTable('App\\Tables\\User'));
		Assert::same('user', $this->mapper->getTable('\\App\\Tables\\User'));
		Assert::same('fakturace_user', $this->mapper->getTable('App\\Tables\\Fakturace\\User'));
		Assert::same('fakturace_user', $this->mapper->getTable('\\App\\Tables\\Fakturace\\User'));
	}

	function testGetEntityClass() {
		Assert::same('App\\Tables\\User', $this->mapper->getEntityClass('user'));
		Assert::same('App\\Tables\\Fakturace\\User', $this->mapper->getEntityClass('fakturace_user'));
		Assert::same('App\\Tables\\Dane\\User', $this->mapper->getEntityClass('dane_user'));
	}

	function testGetColumn() {
		Assert::same('link_name', $this->mapper->getColumn('App\\Tables\\Article', 'linkName'));
	}

	function testGetEntityField() {
		Assert::same('linkName', $this->mapper->getEntityField('article', 'link_name'));
	}

	function testGetRelationshipColumn() {

	}

	function testGetTableByRepositoryClass() {
		Assert::same('user', $this->mapper->getTableByRepositoryClass('App\\Tables\\UserRepository'));
		Assert::same('fakturace_user', $this->mapper->getTableByRepositoryClass('App\\Tables\\Fakturace\\UserRepository'));
	}
}

id(new MapperTest($container))->run();
