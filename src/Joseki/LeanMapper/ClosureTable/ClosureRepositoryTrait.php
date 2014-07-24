<?php

namespace Joseki\LeanMapper\ClosureTable;

use Joseki\LeanMapper\BaseEntity;
use ReflectionClass;

trait ClosureRepositoryTrait
{

    /**
     * Returns ordered path from a leaf to the root (or otherwise, based on $asc value)
     * @param $id
     * @param bool $asc ($asc==true means from leaf to the root)
     * @return BaseEntity[]
     */
    public function getParents($id, $asc = true)
    {
        $table = $this->getTable();
        $closure = $table . '_closure';
        $tableAlias = 'c';
        $closureAlias = 'cc';
        $primaryKey = $this->mapper->getPrimaryKey($table);
        $fluent = $this->connection->command()
            ->select('%n.*', $tableAlias)
            ->from('%n AS %n', $table, $tableAlias)
            ->join('%n AS %n', $closure, $closureAlias)
            ->on('%n.%n = %n.ancestor', $tableAlias, $primaryKey, $closureAlias)
            ->where('%n.descendant = %s', $closureAlias, $id)
            ->orderBy('%n.depth %sql', $closureAlias, (bool)$asc ? 'ASC' : 'DESC');

        return $this->createEntities($fluent->fetchAll());
    }



    /**
     * Return direct children of given root's id
     * @param $id
     * @return BaseEntity[]
     */
    public function getChildren($id)
    {
        $table = $this->getTable();
        $closure = $table . '_closure';
        $tableAlias = 'c';
        $closureAlias = 'cc';
        $primaryKey = $this->mapper->getPrimaryKey($table);
        $fluent = $this->connection->command()
            ->select('%n.*', $tableAlias)
            ->from('%n AS %n', $table, $tableAlias)
            ->join('%n AS %n', $closure, $closureAlias)
            ->on('%n.%n = %n.descendant', $tableAlias, $primaryKey, $closureAlias)
            ->where('%n.ancestor = %s', $closureAlias, $id)
            ->where('%n.depth = 1', $closureAlias);

        $entityClass = $this->mapper->getEntityClass($this->getTable());

        $rc = new ReflectionClass($entityClass);
        if ($rc->implementsInterface('Joseki\LeanMapper\ClosureTable\ISortable')) {
            $fluent->orderBy('%n.order', $tableAlias);
        }

        return $this->createEntities($fluent->fetchAll());
    }



    /**
     * Returns a subtree of nodes (see Node class)
     * @param $id
     * @return Node[]
     */
    public function getSubtree($id)
    {
        $primaryKey = $this->mapper->getPrimaryKey($this->getTable());
        $entities = $this->getSubtreeData($id, $primaryKey);
        $children = [];
        foreach ($entities as $entity) {
            $key = $entity->ancestor->id;
            if (!isset($children[$key])) {
                $children[$key] = [];
            }
            $children[$key][] = $entity;
        }

        return $this->getChildNodes($id, $children, $entities, $primaryKey);
    }



    private function getSubtreeData($value, $primaryKey)
    {
        $table = $this->getTable();
        $closure = $table . '_closure';
        $tableAlias = 'c';
        $firstClosureAlias = 'cc';
        $secondClosureAlias = 'ccc';
        $fluent = $this->connection->command()
            ->select(
                '%n.*, %n.depth, %n.ancestor, %n.descendant',
                $tableAlias,
                $firstClosureAlias,
                $secondClosureAlias,
                $firstClosureAlias
            )
            ->from('%n AS %n', $table, $tableAlias)
            ->join('%n AS %n', $closure, $firstClosureAlias)
            ->on('%n.%n = %n.descendant', $tableAlias, $primaryKey, $firstClosureAlias)
            ->leftJoin('%n AS %n', $closure, $secondClosureAlias)
            ->on('%n.descendant = %n.descendant', $firstClosureAlias, $secondClosureAlias)
            ->where('%n.ancestor = %s', $firstClosureAlias, $value)
            ->where('%n.depth = 1', $secondClosureAlias);

        $entityClass = $this->mapper->getEntityClass($this->getTable());

        $rc = new ReflectionClass($entityClass);
        if ($rc->implementsInterface('Joseki\LeanMapper\ClosureTable\ISortable')) {
            $fluent->orderBy('%n.order', $tableAlias);
        }

        $closureEntity = $entityClass . 'Closure';
        return $this->createEntities($fluent->fetchAll(), $closureEntity);
    }



    private function getChildNodes($id, $children, $entities, $primaryKey)
    {
        $nodes = [];
        if (!isset($children[$id])) {
            return $nodes;
        }
        foreach ($children[$id] as $child) {
            $new = $child->{$primaryKey};
            $nodes[$new] = new Node($entities[$new], $this->getChildNodes($new, $children, $entities, $primaryKey));
        }
        return $nodes;
    }
} 
