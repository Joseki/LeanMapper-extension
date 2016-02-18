<?php

namespace JosekiTests\LeanMapperExtension;

use Joseki\LeanMapper\PackageMapper;
use Nette\Configurator;
use Nette\Utils\Random;
use Tester\Assert;
use Tester\TestCase;

require_once __DIR__ . '/../bootstrap.php';

class MyMapper extends PackageMapper
{

}

/**
 * @testCase
 */
class DIExtensionTest extends TestCase
{

    private function prepareConfigurator()
    {
        $configurator = new Configurator;
        $configurator->setTempDirectory(TEMP_DIR);
        $configurator->addParameters(['container' => ['class' => 'SystemContainer_' . Random::generate()]]);

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



    public function testMapperServiceRedefinition()
    {
        $configurator = $this->prepareConfigurator();
        $configurator->addConfig(__DIR__ . '/config/config.leanmapper.4.neon', $configurator::NONE);

        $config = <<<EOF
services:
  LeanMapper.mapper:
    class: JosekiTests\LeanMapperExtension\MyMapper
EOF;
        $file = \Tester\FileMock::create($config, 'neon');
        $configurator->addConfig($file, $configurator::NONE);

        /** @var \Nette\DI\Container $container */
        $container = $configurator->createContainer();

        /** @var \Joseki\LeanMapper\PackageMapper $mapper */
        $mapper = $container->getByType('Joseki\LeanMapper\PackageMapper');
        Assert::equal('dbo', $mapper->getDefaultSchema());
    }
}

\run(new DIExtensionTest());
