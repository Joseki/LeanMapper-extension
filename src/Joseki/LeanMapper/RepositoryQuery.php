<?php

namespace Joseki\LeanMapper;

use LeanMapperQuery\Query;

class RepositoryQuery extends Query implements \Countable
{
    /** @var IQueryable */
    private $queryable;



    public function __construct(IQueryable $queryable)
    {
        $this->queryable = $queryable;
    }



    public function find()
    {
        return $this->queryable->find($this);
    }



    public function findOne()
    {
        return $this->queryable->findOne($this);
    }



    public function count()
    {
        return $this->queryable->findCount($this);
    }

}
