<?php

namespace JosekiTests\LeanMapperExtension;

use Joseki\LeanMapper\Query;
use Nette\Configurator;
use Nette\Utils\Random;
use Tester\Assert;
use Tester\TestCase;

$container = require __DIR__ . '/../bootstrap.php';

class RepositorySchemaTest extends TestCase
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



    public function testFluentSelect()
    {
        $configurator = $this->prepareConfigurator();

        /** @var \Nette\DI\Container $container */
        $container = $configurator->createContainer();

        /** @var \UnitTests\Tables\BookRepository $tagRepository */
        $bookRepository = $container->getByType('UnitTests\Tables\BookRepository');

        /** @var \UnitTests\Tables\TagRepository $tagRepository */
        $tagRepository = $container->getByType('UnitTests\Tables\TagRepository');

        $fluent = $bookRepository->apply(new Query());
        Assert::same(
            "SELECT `myschema`.`book`.* FROM `myschema`.`book`",
            (string)$fluent
        );

        $fluent = $tagRepository->apply(new Query());
        Assert::same(
            "SELECT `dbo`.`tag`.* FROM `dbo`.`tag`",
            (string)$fluent
        );

        $fluent = $tagRepository->apply((new Query())->where('@id', 5));
        Assert::same(
            "SELECT `dbo`.`tag`.* FROM `dbo`.`tag` WHERE (`dbo`.`tag`.`id` = '5')",
            (string)$fluent
        );

        $fluent = $tagRepository->apply((new Query())->orderBy('@id'));
        Assert::same(
            "SELECT `dbo`.`tag`.* FROM `dbo`.`tag` ORDER BY `dbo`.`tag`.`id`",
            (string)$fluent
        );
    }

}

run(new RepositorySchemaTest());
