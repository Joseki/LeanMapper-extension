<?php

use Nette\Configurator;
use Nette\Utils\Random;
use Tester\Assert;
use UnitTests\Tables\CategoryRepository;

$container = require __DIR__ . '/../bootstrap.php';

class RepositoryTest extends Tester\TestCase
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
        $configurator->addConfig(__DIR__ . '/config/config.leanmapper.3.neon', $configurator::NONE);

        /** @var \Nette\DI\Container $container */
        $container = $configurator->createContainer();

        /** @var \UnitTests\Tables\CategoryRepository $repository */
        $repository = $container->getService('LeanMapper.table.category');

        Assert::true($repository instanceof CategoryRepository);

        $container->getByType('\DibiConnection')->loadFile(__DIR__ . '/db/page-dump.sql');

        Assert::equal(
            ['PC' => 1, 'Printer' => 2, 'Laser' => 7, 'Ink' => 4, 'Mouse' => 5, 'Optic' => 6, 'Keyboard' => 8, 'Wired' => 9, 'Wireless' => 10, '3-Bottons' => 11, 'More-Bottons' => 12,],
            $repository->findPairsBy('name', 'id')
        );
    }

}

\Tester\Environment::lock('database', LOCK_DIR);
run(new RepositoryTest());
