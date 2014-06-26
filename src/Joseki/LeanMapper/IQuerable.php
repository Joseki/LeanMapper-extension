<?php

namespace Joseki\LeanMapper;

use LeanMapperQuery\IQuery;
use LeanMapper\Entity;

interface IQueryable
{

    /**
     * @param IQuery $query
     * @return Entity[]
     */
    public function findBy(IQuery $query);



    /**
     * @param null $limit
     * @param null $offset
     * @return Entity[]
     */
    public function findAll($limit = null, $offset = null);



    /**
     * @param IQuery $query
     * @return Entity
     */
    public function findOneBy(IQuery $query);



    /**
     * @param IQuery $query
     * @return int
     */
    public function findCountBy(IQuery $query);
}
