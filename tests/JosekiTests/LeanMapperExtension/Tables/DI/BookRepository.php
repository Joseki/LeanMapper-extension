<?php

namespace UnitTests\Tables;

use Joseki\LeanMapper\Query;
use Joseki\LeanMapper\Repository;
use LeanMapperQuery\IQuery;

/**
 * @method Book get($id)
 * @method Book findOneBy(IQuery $query)
 * @method Book[] findAll($limit = null, $offset = null)
 * @method Book[] findBy(IQuery $query)
 * @method Book[] findCountBy(IQuery $query)
 */
class BookRepository extends Repository
{

    public function apply(Query $query)
    {
        return parent::apply($query);
    }

}
