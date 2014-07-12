<?php

use Dibi\Bridges\Tracy\Panel;
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
        $children = [];
        foreach ($data as $node) {
            $children[$node->data->id] = $this->simplifieTree($node->children);
        }
        return $children;
    }



    public function testLeanMapperTree()
    {
        $tree = $this->repository->getSubtree(1);
        $actual = $this->simplifieTree($tree);
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
        $actual = $this->simplifieTree($tree);
        $expected = [
            6 => [
                11 => [],
                12 => [],
            ],
            7 => [],
        ];
        Assert::equal($expected, $actual);

        $tree = $this->repository->getSubtree(7); // list
        $actual = $this->simplifieTree($tree);
        $expected = [];
        Assert::equal($expected, $actual);

        $tree = $this->repository->getSubtree(50); // does not exist
        $actual = $this->simplifieTree($tree);
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


\Tracy\Debugger::enable(false);
\Tracy\Debugger::$maxDepth = 10;

$test = new ClosureTableTest($container);
$panel = new Panel();
$panel->register($test->connection);
id($test)->run();

