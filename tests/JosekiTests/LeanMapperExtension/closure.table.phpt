<?php

/**
 * TEST: closure table test.
 *
 * @phpVersion 5.4
 */

use JosekiTests\LeanMapperExtension\MapperMock;
use JosekiTests\LeanMapperExtension\ServiceLocator;
use JosekiTests\LeanMapperExtension\Tables\CategoryRepository;
use LeanMapper\DefaultEntityFactory;
use Tester\Assert;

$container = require __DIR__ . '/../bootstrap.php';
require __DIR__ . '/ServiceLocator.php';

class ClosureTableTest extends Tester\TestCase
{
    /** @var  LeanMapper\Connection */
    public $connection;

    /** @var  MapperMock */
    private $mapper;

    /** @var  DefaultEntityFactory */
    private $entityFactory;

    /** @var  CategoryRepository */
    private $repository;



    function __construct()
    {
        $this->connection = ServiceLocator::getConnection();
        $this->connection->loadFile(__DIR__ . '/db/page-dump.sql');
    }



    function setUp()
    {
        $this->mapper = ServiceLocator::getMapper();
        $this->entityFactory = new DefaultEntityFactory();
        $this->repository = new CategoryRepository($this->connection, $this->mapper, $this->entityFactory);
    }



    private function simplifieTree($data)
    {
        $children = array();
        foreach ($data as $node) {
            $children[$node->data->id] = $this->simplifieTree($node->children);
        }
        return $children;
    }



    public function testLeanMapperTree()
    {
        $tree = $this->repository->getSubtree(1);
        $actual = $this->simplifieTree($tree);
        $expected = array(
            2 => array(
                3 => array(),
                4 => array(),
            ),
            5 => array(
                6 => array(
                    11 => array(),
                    12 => array(),
                ),
                7 => array(),
            ),
            8 => array(
                9 => array(),
                10 => array(),
            ),
        );
        Assert::equal($expected, $actual);

        $tree = $this->repository->getSubtree(5);
        $actual = $this->simplifieTree($tree);
        $expected = array(
            6 => array(
                11 => array(),
                12 => array(),
            ),
            7 => array(),
        );
        Assert::equal($expected, $actual);

        $tree = $this->repository->getSubtree(7); // list
        $actual = $this->simplifieTree($tree);
        $expected = array();
        Assert::equal($expected, $actual);

        $tree = $this->repository->getSubtree(50); // does not exist
        $actual = $this->simplifieTree($tree);
        $expected = array();
        Assert::equal($expected, $actual);
    }



    public function testParents()
    {
        $entities = $this->repository->getParents(4);
        $actual = array_keys($entities);
        $expected = array(4, 2, 1);
        Assert::equal($expected, $actual);

        $entities = $this->repository->getParents(12);
        $actual = array_keys($entities);
        $expected = array(12, 6, 5, 1);
        Assert::equal($expected, $actual);

        Assert::equal("  SELECT `c`.* FROM `category` AS `c` JOIN `category_closure` AS `cc` ON `c`.`id` = `cc`.ancestor WHERE `cc`.descendant = '12' ORDER BY `cc`.depth ASC", dibi::$sql);
    }



    public function testChildren()
    {
        $entities = $this->repository->getChildren(4);
        $actual = array_keys($entities);
        $expected = array();
        Assert::equal($expected, $actual);

        $entities = $this->repository->getChildren(1);
        $actual = array_keys($entities);
        $expected = array(2, 5, 8);
        Assert::equal($expected, $actual);

        $entities = $this->repository->getChildren(6);
        $actual = array_keys($entities);
        $expected = array(11, 12);
        Assert::equal($expected, $actual);
    }
}

\Tester\Environment::lock('database', LOCK_DIR);
id(new ClosureTableTest)->run();

