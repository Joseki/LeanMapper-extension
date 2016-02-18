<?php

namespace Joseki\LeanMapper;

use LeanMapper\Entity;
use LeanMapper\Repository as LR;
use Nette\Utils\Callback;
use Nette\Utils\Paginator;

/**
 * @property array $onBeforePersist
 * @property array $onBeforeCreate
 * @property array $onBeforeUpdate
 * @property array $onBeforeDelete
 * @property array $onAfterPersist
 * @property array $onAfterCreate
 * @property array $onAfterUpdate
 * @property array $onAfterDelete
 */
abstract class Repository extends LR
{

    protected function apply(Query $query)
    {
        $fluent = $this->createFluent();
        $query->applyQuery($fluent, $this->mapper);
        return $fluent;
    }



    /**
     * @return Query
     */
    public function createQuery()
    {
        return new Query();
    }



    /**
     * @param Query $query
     * @return Entity[]
     */
    public function findBy(Query $query)
    {
        return $this->createEntities($this->apply($query)->fetchAll());
    }



    /**
     * @param Query $query
     * @return Entity
     * @throws NotFoundException
     */
    public function findOneBy(Query $query)
    {
        $row = $this->apply($query)
            ->removeClause('LIMIT')
            ->removeClause('OFFSET')
            ->fetch();
        if ($row === false) {
            throw new NotFoundException(sprintf('Entity not found in sql \n%s', \dibi::$sql));
        }
        return $this->createEntity($row);
    }



    /**
     * @param int|null $limit
     * @param int|null $offset
     * @return Entity[]
     */
    public function findAll($limit = null, $offset = null)
    {
        $query = $this->createQuery();
        if ($limit) {
            $query->limit($limit);
        }
        if ($offset) {
            $query->offset($offset);
        }
        return $this->findBy($query);
    }



    /**
     * @param Query $query
     * @return int
     */
    public function findCountBy(Query $query)
    {
        return $this->apply($query)->count();
    }



    /**
     * @param Query $query
     * @param $page
     * @param $itemsPerPage
     * @return int
     */
    public function findPageBy(Query $query = null, $page, $itemsPerPage)
    {
        if ($query === null) {
            $query = new Query();
        }
        $paginator = new Paginator();
        $paginator->itemCount = $this->findCountBy($query);
        $paginator->itemsPerPage = $itemsPerPage;
        $paginator->page = $page;
        $query->limit($itemsPerPage)->offset($paginator->offset);
        return $this->findBy($query);
    }



    public function findPairsBy($key, $value, Query $query = null)
    {
        $class = $this->mapper->getEntityClass($this->getTable());
        $key = $this->mapper->getColumn($class, $key);
        $value = $this->mapper->getColumn($class, $value);

        if (!$query) {
            $query = $this->createQuery();
        }
        return $this->apply($query)->fetchPairs($key, $value);
    }



    /**
     * @param $id
     * @return Entity
     * @throws NotFoundException
     */
    public function get($id)
    {
        $PK = $this->mapper->getPrimaryKey($this->getTable());
        $query = $this->createQuery()->where("@$PK", $id);
        return $this->findOneBy($query);
    }



    public function inTransaction($callback, array $args = [])
    {
        Callback::check($callback);
        $this->connection->begin();
        try {
            $result = Callback::invokeArgs($callback, $args);
        } catch (\Exception $e) {
            $this->connection->rollback();
            throw $e;
        }
        $this->connection->commit();

        return $result;
    }
}
