<?php

namespace JosekiTests\LeanMapperExtension;

use Nette\Configurator;
use Nette\Utils\Random;
use Tester\Assert;

require_once __DIR__ . '/../bootstrap.php';

/**
 * @testCase
 */
class PackageMapperTest extends \Tester\TestCase
{

    private function prepareConfigurator()
    {
        $configurator = new Configurator;
        $configurator->setTempDirectory(TEMP_DIR);
        $configurator->addParameters(['container' => ['class' => 'SystemContainer_' . Random::generate()]]);

        $configurator->addConfig(__DIR__ . '/config/config.local.neon', $configurator::NONE);

        return $configurator;
    }



    public function testEntityClassGetter()
    {
        $configurator = $this->prepareConfigurator();
        $configurator->addConfig(__DIR__ . '/config/config.leanmapper.1.neon', $configurator::NONE);

        /** @var \Nette\DI\Container $container */
        $container = $configurator->createContainer();

        /** @var \Joseki\LeanMapper\PackageMapper $mapper */
        $mapper = $container->getByType('Joseki\LeanMapper\PackageMapper');
        Assert::true($mapper instanceof \Joseki\LeanMapper\PackageMapper);

        Assert::equal('UnitTests\Tables\Book', $mapper->getEntityClass('book'));
        Assert::equal('UnitTests\Tables\Tag', $mapper->getEntityClass('tag'));
    }



    public function testCustomTableNames()
    {
        $configurator = $this->prepareConfigurator();
        $configurator->addConfig(__DIR__ . '/config/config.leanmapper.2.neon', $configurator::NONE);

        /** @var \Nette\DI\Container $container */
        $container = $configurator->createContainer();

        /** @var \Joseki\LeanMapper\PackageMapper $mapper */
        $mapper = $container->getByType('Joseki\LeanMapper\PackageMapper');
        Assert::true($mapper instanceof \Joseki\LeanMapper\PackageMapper);

        Assert::equal('UnitTests\Tables\Book', $mapper->getEntityClass('fantasy_book'));
        Assert::equal('UnitTests\Tables\Tag', $mapper->getEntityClass('tag'));

        Assert::equal('fantasy_book', $mapper->getTable('UnitTests\Tables\Book'));
        Assert::equal('tag', $mapper->getTable('UnitTests\Tables\Tag'));

        Assert::equal('fantasy_book', $mapper->getTableByRepositoryClass('UnitTests\Tables\BookRepository'));
        Assert::equal('tag', $mapper->getTableByRepositoryClass('UnitTests\Tables\TagRepository'));

        Assert::equal('fantasy_book_tag', $mapper->getRelationshipTable('fantasy_book', 'tag'));
    }
}

\run(new PackageMapperTest());
