<?php

use Nette\Configurator;
use Nette\Utils\Callback;
use Nette\Utils\Random;
use Tester\Assert;
use UnitTests\Tables\CategoryRepository;

$container = require __DIR__ . '/../bootstrap.php';

class TransactionsTest extends Tester\TestCase
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
        $container->getByType('\DibiConnection')->loadFile(__DIR__ . '/db/page-dump.sql');

        /** @var \UnitTests\Tables\CategoryRepository $repository */
        $repository = $container->getService('LeanMapper.table.category');

        Assert::true($repository instanceof CategoryRepository);

        $callback = function ($id) use ($repository) {
            $category = new \UnitTests\Tables\Category();
            $category->name = 'Transaction' . $id;
            $category->id = $id;
            $repository->persist($category);
        };
        $errorCallback = function ($id) use ($repository, $callback) {
            Callback::invokeArgs($callback, [$id]);
            throw new Exception;
        };
        $deepCallback = function ($id1, $id2, $id3) use ($repository, $callback, $errorCallback) {
            $repository->inTransaction($callback, [$id1]);
            try {
                $repository->inTransaction($errorCallback, [$id2]);
            } catch (\Exception $e) {

            }
            $repository->inTransaction($callback, [$id3]);
        };

        $repository->inTransaction($callback, [1000]);
        Assert::equal(1, $repository->findCountBy($repository->createQuery()->where('@id', 1000)));

        Assert::exception(
            function () use($repository, $errorCallback) {
                $repository->inTransaction($errorCallback, [1001]);
            },
            'Exception'
        );
        Assert::equal(0, $repository->findCountBy($repository->createQuery()->where('@id', 1001)));

        $repository->inTransaction($deepCallback, [2000, 2001, 2002]);
        Assert::equal(1, $repository->findCountBy($repository->createQuery()->where('@id', 2000)));
        Assert::equal(0, $repository->findCountBy($repository->createQuery()->where('@id', 2001)));
        Assert::equal(1, $repository->findCountBy($repository->createQuery()->where('@id', 2002)));
    }

}

\Tester\Environment::lock('database', LOCK_DIR);
id((new TransactionsTest))->run();
