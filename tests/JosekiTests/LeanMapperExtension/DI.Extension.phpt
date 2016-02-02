<?php

namespace JosekiTests\Migration;

use Nette\Configurator;
use Nette\Utils\Random;
use Tester\Assert;

require_once __DIR__ . '/../bootstrap.php';

/**
 * @testCase
 */
class DIExtensionTest extends \Tester\TestCase
{

    private function prepareConfigurator()
    {
        $configurator = new Configurator;
        $configurator->setTempDirectory(TEMP_DIR);
        $configurator->addParameters(array('container' => array('class' => 'SystemContainer_' . Random::generate())));

        $configurator->addConfig(__DIR__ . '/config/config.local.neon', $configurator::NONE);

        return $configurator;
    }



    public function testRepository()
    {
        $configurator = $this->prepareConfigurator();
        $configurator->addConfig(__DIR__ . '/config/config.leanmapper.1.neon', $configurator::NONE);

        /** @var \Nette\DI\Container $container */
        $container = $configurator->createContainer();

        Assert::equal(2, count($container->findByType('Joseki\LeanMapper\Repository')));
    }



    public function testRepositoryMap()
    {
        $configurator = $this->prepareConfigurator();
        $configurator->addConfig(__DIR__ . '/config/config.leanmapper.2.neon', $configurator::NONE);

        /** @var \Nette\DI\Container $container */
        $container = $configurator->createContainer();

        Assert::equal(2, count($container->findByType('Joseki\LeanMapper\Repository')));
    }



    public function testRepositorySchemaMap()
    {
        $configurator = $this->prepareConfigurator();
        $configurator->addConfig(__DIR__ . '/config/config.leanmapper.4.neon', $configurator::NONE);

        /** @var \Nette\DI\Container $container */
        $container = $configurator->createContainer();

        Assert::equal(2, count($container->findByType('Joseki\LeanMapper\Repository')));

        /** @var \Joseki\LeanMapper\PackageMapper $mapper */
        $mapper = $container->getByType('Joseki\LeanMapper\PackageMapper');
        Assert::equal('myschema.book', $mapper->getTableByRepositoryClass('UnitTests\Tables\BookRepository'));
        Assert::equal('dbo.tag', $mapper->getTableByRepositoryClass('UnitTests\Tables\TagRepository'));
    }
}

\run(new DIExtensionTest());
