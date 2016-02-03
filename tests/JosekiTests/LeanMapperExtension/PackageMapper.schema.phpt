<?php

use Nette\Configurator;
use Nette\Utils\Random;
use Tester\Assert;

$container = require __DIR__ . '/../bootstrap.php';

class PackageMapperSchemaTest extends Tester\TestCase
{

    private function prepareConfigurator()
    {
        $configurator = new Configurator;
        $configurator->setTempDirectory(TEMP_DIR);
        $configurator->addParameters(['container' => ['class' => 'SystemContainer_' . Random::generate()]]);

        $configurator->addConfig(__DIR__ . '/config/config.local.neon', $configurator::NONE);
        $configurator->addConfig(__DIR__ . '/config/config.leanmapper.4.neon', $configurator::NONE);

        return $configurator;
    }



    public function testMapper()
    {
        $configurator = $this->prepareConfigurator();

        /** @var \Nette\DI\Container $container */
        $container = $configurator->createContainer();

        $mapper = $container->getByType('LeanMapper\IMapper');
        Assert::same('myschema.book', $mapper->getTable('UnitTests\Tables\Book'));
        Assert::same('dbo.tag', $mapper->getTable('UnitTests\Tables\Tag'));

        Assert::same('UnitTests\Tables\Book', $mapper->getEntityClass('myschema.book'));
        Assert::same('UnitTests\Tables\Tag', $mapper->getEntityClass('dbo.tag'));

        Assert::same('UnitTests\Tables\Book', $mapper->getEntityClass('book'));
        Assert::same('UnitTests\Tables\Tag', $mapper->getEntityClass('tag'));
    }

}

run(new PackageMapperSchemaTest());
