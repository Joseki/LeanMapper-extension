<?php

namespace Joseki\LeanMapper;

use LeanMapper\Connection;
use LeanMapper\Entity;
use LeanMapper\Exception\InvalidArgumentException;
use LeanMapper\IMapper;
use LeanMapper\Repository as LR;
use LeanMapperQuery\IQuery;

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
abstract class Repository extends LR implements IQueryable
{

    private function apply(IQuery $query)
    {
        $fluent = $this->createFluent();
        $query->applyQuery($fluent, $this->mapper);
        return $fluent;
    }



    public function createQuery()
    {
        return new RepositoryQuery($this);
    }



    /**
     * @param IQuery $query
     * @return Entity[]
     */
    public function findBy(IQuery $query)
    {
        return $this->createEntities($this->apply($query)->fetchAll());
    }



    /**
     * @param null $limit
     * @param null $offset
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
     * @param IQuery $query
     * @throws NotFoundException
     * @return Entity|NULL
     */
    public function findOneBy(IQuery $query)
    {
        $row = $this->apply($query)
            ->removeClause('limit')
            ->removeClause('offset')
            ->fetch();
        if ($row === null) {
            throw new NotFoundException;
        }
        return $this->createEntity($row);
    }



    public function get($id)
    {
        $PK = $this->mapper->getPrimaryKey($this->getTable());
        $query = $this->createQuery()->where("$PK", $id);
        return $this->findOneBy($query);
    }



    /**
     * @param IQuery $query
     * @return int
     */
    public function findCountBy(IQuery $query)
    {
        return count($this->apply($query));
    }

}
