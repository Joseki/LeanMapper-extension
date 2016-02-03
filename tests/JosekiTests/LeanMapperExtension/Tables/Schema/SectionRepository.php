<?php

namespace UnitTests\Tables;

use Joseki\LeanMapper\ClosureTable\ClosureRepositoryTrait;
use Joseki\LeanMapper\Query;
use Joseki\LeanMapper\Repository;

/**
 * @method Section get($id)
 * @method Section findOneBy(Query $query)
 * @method Section[] findBy(Query $query)
 * @method Section[] findAll($limit = null, $offset = null)
 * @method Section[] findPageBy(Query $query, $page, $itemsPerPage)
 * @method Section[] getParent($id, $asc = true)
 * @method Section[] getChildren($id)
 * @method int findCountBy(Query $query)
 */
class SectionRepository extends Repository
{
    use ClosureRepositoryTrait;



    public function moveNodeTo($node, $parent)
    {
        $table = $this->getTable();
        $closure = $table . '_closure';
        $A = 'ct_a';
        $B = 'ct_b';
        $C = 'ct_c';

        $this->connection->query(
            "DELETE %n FROM %n %n
            JOIN %n %n USING(descendant)
            LEFT JOIN %n %n ON %n.ancestor = %n.ancestor AND %n.descendant = %n.ancestor
            WHERE %n.ancestor = %s AND %n.ancestor IS NULL",
            $A,
            $closure,
            $A,
            $closure,
            $B,
            $closure,
            $C,
            $C,
            $B,
            $C,
            $A,
            $B,
            $node,
            $C
        );

        $this->connection->query(
            "INSERT INTO %n (ancestor, descendant, depth) (%sql)",
            $closure,
            [
                'SELECT %n.ancestor, %n.descendant, %n.depth+%n.depth+1
                FROM %n %n
                JOIN %n %n
                WHERE %n.ancestor = %s AND %n.descendant = %s',
                $A,
                $B,
                $A,
                $B,
                $closure,
                $A,
                $closure,
                $B,
                $B,
                $node,
                $A,
                $parent
            ]
        );
    }



    public function createNode($id, $parent)
    {
        $table = $this->getTable();
        $closure = $table . '_closure';

        $this->connection->query(
            "INSERT INTO %n (ancestor, descendant, depth) VALUES (%s, %s, 0)",
            $closure,
            $id,
            $id
        );
        $this->connection->query(
            "INSERT INTO %n (ancestor, descendant, depth)
            SELECT ancestor, %s, depth+1 FROM %n WHERE descendant = %s",
            $closure,
            $id,
            $closure,
            $parent
        );
    }
}




