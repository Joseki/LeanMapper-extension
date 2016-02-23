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
        return $query->applyQuery($this->createFluent(), $this->mapper);
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
        $fluent = $this->apply($query);
        if (($fluent->_export('limit') || $fluent->_export('offset')) && !$fluent->_export('orderBy')) {
            $fluent->orderBy($this->mapper->getPrimaryKey($this->getTable()));
        }

        return $this->createEntities($fluent->fetchAll());
    }



    /**
     * @param Query $query
     * @return Entity
     * @throws NotFoundException
     */
    public function findOneBy(Query $query)
    {
        $fluent = $this->apply($query);
        if (!$fluent->_export('orderBy')) {
            $fluent->orderBy($this->mapper->getPrimaryKey($this->getTable()));
        }
        $row = $fluent->fetch();

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



    /**
     * @param $key
     * @param $value
     * @param Query|null $query
     * @return array
     */
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
        $primaryKey = $this->mapper->getPrimaryKey($this->getTable());
        $column = $this->mapper->getColumn($this->mapper->getEntityClass($this->getTable()), $primaryKey);
        $query = $this->createQuery()->where("@$column", $id);
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
