<?php

/**
 * TEST: closure table test.
 *
 * @phpVersion 5.4
 * @testCase
 */

namespace JosekiTests\LeanMapperExtension;

use Nette\Configurator;
use Nette\Utils\Random;
use Tester\Assert;
use Tester\TestCase;
use UnitTests\Tables\CategoryRepository;

$container = require __DIR__ . '/../bootstrap.php';

class ClosureRepositoryTraitTest extends TestCase
{

    /** @var  CategoryRepository */
    private $repository;



    private function prepareConfigurator()
    {
        $configurator = new Configurator;
        $configurator->setTempDirectory(TEMP_DIR);
        $configurator->addParameters(['container' => ['class' => 'SystemContainer_' . Random::generate()]]);

        $configurator->addConfig(__DIR__ . '/config/config.local.neon', $configurator::NONE);
        $configurator->addConfig(__DIR__ . '/config/config.leanmapper.3.neon', $configurator::NONE);

        return $configurator;
    }



    function setUp()
    {
        $configurator = $this->prepareConfigurator();

        /** @var \Nette\DI\Container $container */
        $container = $configurator->createContainer();

        $connection = $container->getService('LeanMapper.connection');
        $connection->loadFile(__DIR__ . '/db/page-dump.sql');

        $this->repository = $container->getService('LeanMapper.table.category');
    }



    private function simplifyTree($data)
    {
        $children = [];
        foreach ($data as $node) {
            $children[$node->data->id] = $this->simplifyTree($node->children);
        }
        return $children;
    }



    public function testLeanMapperTree()
    {
        $tree = $this->repository->getSubtree(1);
        $actual = $this->simplifyTree($tree);
        $expected = [
            2 => [
                3 => [],
                4 => [],
            ],
            5 => [
                6 => [
                    11 => [],
                    12 => [],
                ],
                7 => [],
            ],
            8 => [
                9 => [],
                10 => [],
            ],
        ];
        Assert::equal($expected, $actual);

        $tree = $this->repository->getSubtree(5);
        $actual = $this->simplifyTree($tree);
        $expected = [
            6 => [
                11 => [],
                12 => [],
            ],
            7 => [],
        ];
        Assert::equal($expected, $actual);

        $tree = $this->repository->getSubtree(7); // list
        $actual = $this->simplifyTree($tree);
        $expected = [];
        Assert::equal($expected, $actual);

        $tree = $this->repository->getSubtree(50); // does not exist
        $actual = $this->simplifyTree($tree);
        $expected = [];
        Assert::equal($expected, $actual);
    }



    public function testParents()
    {
        $entities = $this->repository->getParents(4);
        $actual = array_keys($entities);
        $expected = [4, 2, 1];
        Assert::equal($expected, $actual);

        $entities = $this->repository->getParents(12);
        $actual = array_keys($entities);
        $expected = [12, 6, 5, 1];
        Assert::equal($expected, $actual);

        Assert::equal(
            "  SELECT `c`.* FROM `category` AS `c` JOIN `category_closure` AS `cc` ON `c`.`id` = `cc`.ancestor WHERE `cc`.descendant = '12' ORDER BY `cc`.depth ASC",
            \dibi::$sql
        );
    }



    public function testChildren()
    {
        $entities = $this->repository->getChildren(4);
        $actual = array_keys($entities);
        $expected = [];
        Assert::equal($expected, $actual);

        $entities = $this->repository->getChildren(1);
        $actual = array_keys($entities);
        $expected = [2, 5, 8];
        Assert::equal($expected, $actual);

        $entities = $this->repository->getChildren(6);
        $actual = array_keys($entities);
        $expected = [11, 12];
        Assert::equal($expected, $actual);
    }
}

\Tester\Environment::lock('database', LOCK_DIR);
run(new ClosureRepositoryTraitTest());
