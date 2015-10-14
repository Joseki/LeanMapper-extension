<?php

namespace Joseki\LeanMapper\ClosureTable;

use Joseki\LeanMapper\BaseEntity;
use LeanMapper\Entity;
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
        $closureEntityClass = $entityClass . 'Closure';
        if ($rc->implementsInterface('Joseki\LeanMapper\ClosureTable\ISortable')) {
            $fluent->orderBy('%n.order', $tableAlias);
        } else if (class_exists($closureEntityClass)) {
            /** @var Entity $closureEntity */
            $closureEntity = new $closureEntityClass;
            if ($closureEntity->getReflection($this->mapper)->getEntityProperty('order')) {
                $fluent->orderBy('%n.order', $closureAlias);
            }
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
        $children = array();
        foreach ($entities as $entity) {
            $key = $entity->ancestor->id;
            if (!isset($children[$key])) {
                $children[$key] = array();
            }
            $children[$key][] = $entity;
        }

        return $this->getChildNodes($id, $children, $entities, $primaryKey);
    }



    /**
     * @internal
     * @return Entity[]
     */
    public function getSubtreeData($value, $primaryKey)
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
        $closureEntityClass = $entityClass . 'Closure';
        if ($rc->implementsInterface('Joseki\LeanMapper\ClosureTable\ISortable')) {
            $fluent->orderBy('%n.order', $tableAlias);
        } else if (class_exists($closureEntityClass)) {
            /** @var Entity $closureEntity */
            $closureEntity = new $closureEntityClass;
            if ($closureEntity->getReflection($this->mapper)->getEntityProperty('order')) {
                $fluent->orderBy('%n.order', $firstClosureAlias);
            }
        }

        return $this->createEntities($fluent->fetchAll(), $closureEntityClass);
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



    public function moveNodeTo($node, $parent)
    {
        $table = $this->getTable();
        $closure = $table . '_closure';
        $A = 'ct_a';
        $B = 'ct_b';
        $C = 'ct_c';

        $query = "DELETE %n FROM %n %n
            JOIN %n %n USING(descendant)
            LEFT JOIN %n %n ON %n.ancestor = %n.ancestor AND %n.descendant = %n.ancestor
            WHERE %n.ancestor = %s AND %n.ancestor IS NULL";
        $this->connection->query($query, $A, $closure, $A, $closure, $B, $closure, $C, $C, $B, $C, $A, $B, $node, $C);

        $query = "INSERT INTO %n (ancestor, descendant, depth) (%sql)";
        $subQuery = 'SELECT %n.ancestor, %n.descendant, %n.depth+%n.depth+1 FROM %n %n JOIN %n %n WHERE %n.ancestor = %s AND %n.descendant = %s';
        $this->connection->query($query, $closure, [$subQuery, $A, $B, $A, $B, $closure, $A, $closure, $B, $B, $node, $A, $parent]);
    }



    public function createNode($id, $parent)
    {
        $table = $this->getTable();
        $closure = $table . '_closure';

        $query = "INSERT INTO %n (ancestor, descendant, depth) VALUES (%s, %s, 0)";
        $this->connection->query($query, $closure, $id, $id);

        $query = "INSERT INTO %n (ancestor, descendant, depth) SELECT ancestor, %s, depth+1 FROM %n WHERE descendant = %s";
        $this->connection->query($query, $closure, $id, $closure, $parent);
    }
} 
