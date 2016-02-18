<?php

namespace JosekiTests\LeanMapperExtension;

use Joseki\LeanMapper\PackageMapper;
use LeanMapper\Entity;
use Nette\Configurator;
use Nette\Utils\Random;
use Tester\Assert;
use Tester\TestCase;

$container = require __DIR__ . '/../bootstrap.php';

class PackageMapperSchemaTest extends TestCase
{

    private function prepareConfigurator()
    {
        $configurator = new Configurator;
        $configurator->setTempDirectory(TEMP_DIR);
        $configurator->addParameters(['container' => ['class' => 'SystemContainer_' . Random::generate()]]);

        $configurator->addConfig(__DIR__ . '/config/config.local.neon', $configurator::NONE);

        return $configurator;
    }



    public function testMapperTable()
    {
        $configurator = $this->prepareConfigurator();
        $configurator->addConfig(__DIR__ . '/config/config.leanmapper.4.neon', $configurator::NONE);

        /** @var \Nette\DI\Container $container */
        $container = $configurator->createContainer();

        /** @var \Joseki\LeanMapper\PackageMapper $mapper */
        $mapper = $container->getByType('Joseki\LeanMapper\PackageMapper');
        Assert::same('myschema.book', $mapper->getTable('UnitTests\Tables\Book'));
        Assert::same('dbo.tag', $mapper->getTable('UnitTests\Tables\Tag'));

        Assert::same('UnitTests\Tables\Book', $mapper->getEntityClass('myschema.book'));
        Assert::same('UnitTests\Tables\Tag', $mapper->getEntityClass('dbo.tag'));

        Assert::same('UnitTests\Tables\Book', $mapper->getEntityClass('book'));
        Assert::same('UnitTests\Tables\Tag', $mapper->getEntityClass('tag'));
    }



    public function testMapperEntity()
    {
        $configurator = $this->prepareConfigurator();
        $configurator->addConfig(__DIR__ . '/config/config.leanmapper.5.neon', $configurator::NONE);

        /** @var \Nette\DI\Container $container */
        $container = $configurator->createContainer();

        /** @var \Joseki\LeanMapper\PackageMapper $mapper */
        $mapper = $container->getByType('Joseki\LeanMapper\PackageMapper');

        Assert::same('id', $mapper->getColumn('UnitTests\Tables\Permission', 'id'));
        Assert::same('role', $mapper->getColumn('UnitTests\Tables\Permission', 'role'));
        Assert::same('section', $mapper->getColumn('UnitTests\Tables\Permission', 'section'));

        $roleEntity = new \UnitTests\Tables\Permission;
        $reflection = $roleEntity->getReflection($mapper);
        $property = $reflection->getEntityProperty('role');

        Assert::true($property->hasRelationship());

        /** @var \LeanMapper\Relationship\HasOne $relationship */
        $relationship = $property->getRelationship();
        Assert::true($relationship instanceof \LeanMapper\Relationship\HasOne);

        $targetEntityClass = $property->getType();
        Assert::same('UnitTests\Tables\Role', $targetEntityClass);

        $targetTable = $mapper->getTable($targetEntityClass);
        Assert::same('dbo.role', $targetTable);

        $roleClass = $mapper->getEntityClass($targetTable);
        /** @var Entity $roleEntity */
        $roleEntity = new $roleClass;
        $rolePrimaryKey = $mapper->getPrimaryKey($targetTable);
        $roleProperty = $roleEntity->getReflection($mapper)->getEntityProperty($rolePrimaryKey);

        Assert::same('string', $roleProperty->getType());
        Assert::same('role', $relationship->getColumnReferencingTargetTable());
    }



    public function testRelationshipTable()
    {
        $mapper = new PackageMapper();
        $mapper->setDefaultSchema('dbo');
        $sourceTable = 'dbo.user';
        $targetTable = 'dbo.role';
        Assert::same('dbo.user_role', $mapper->getRelationshipTable($sourceTable, $targetTable));
    }



    public function testRelationshipColumn()
    {
        $mapper = new PackageMapper([], [], 'dbo');
        $sourceTable = 'dbo.user';
        $targetTable = 'dbo.role';
        Assert::same('role', $mapper->getRelationshipColumn($sourceTable, $targetTable));
    }

}

run(new PackageMapperSchemaTest());
