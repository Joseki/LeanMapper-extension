<?php

namespace Joseki\LeanMapper;

use LeanMapper\Entity;
use LeanMapper\Repository as LR;
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
     * @param Query|string $query
     * @return Entity[]
     * @throws InvalidArgumentException
     */
    public function findBy($query)
    {
        if (func_num_args() > 1) {
            $query = $this->createQuery();
            call_user_func_array(array($query, 'where'), func_get_args());
        }
        if (!$query instanceof Query) {
            if (is_object($query)) {
                $class = get_class($query);
                throw new InvalidArgumentException("Exptected instance of '\\Joseki\\LeanMapper\\Query', instance of '$class' given.");
            } else {
                $type = gettype($query);
                throw new InvalidArgumentException("Exptected instance of '\\Joseki\\LeanMapper\\Query', '$type' given.");
            }
        }

        return $this->createEntities($this->apply($query)->fetchAll());
    }



    /**
     * @param Query|string $query
     * @return Entity
     * @throws InvalidArgumentException
     * @throws NotFoundException
     */
    public function findOneBy($query)
    {
        if (func_num_args() > 1) {
            $query = $this->createQuery();
            call_user_func_array(array($query, 'where'), func_get_args());
        }
        if (!$query instanceof Query) {
            if (is_object($query)) {
                $class = get_class($query);
                throw new InvalidArgumentException("Exptected instance of '\\Joseki\\LeanMapper\\Query', instance of '$class' given.");
            } else {
                $type = gettype($query);
                throw new InvalidArgumentException("Exptected instance of '\\Joseki\\LeanMapper\\Query', '$type' given.");
            }
        }
        $row = $this->apply($query)
            ->removeClause('limit')
            ->removeClause('offset')
            ->fetch();
        if ($row === false) {
            throw new NotFoundException('Entity not found.');
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
}
