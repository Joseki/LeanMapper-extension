<?php

namespace UnitTests\Tables;

use Joseki\LeanMapper\Query;
use Joseki\LeanMapper\Repository;
use LeanMapperQuery\IQuery;

/**
 * @method Tag get($id)
 * @method Tag findOneBy(IQuery $query)
 * @method Tag[] findAll($limit = null, $offset = null)
 * @method Tag[] findBy(IQuery $query)
 * @method Tag[] findCountBy(IQuery $query)
 */
class TagRepository extends Repository
{

    public function apply(Query $query)
    {
        return parent::apply($query);
    }
}
