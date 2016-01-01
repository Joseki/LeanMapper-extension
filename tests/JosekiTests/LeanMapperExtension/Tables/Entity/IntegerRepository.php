<?php

namespace UnitTests\Tables;

use Joseki\LeanMapper\Repository;
use LeanMapperQuery\IQuery;

/**
 * @method Integer get($id)
 * @method Integer findOneBy(IQuery $query)
 * @method Integer[] findAll($limit = null, $offset = null)
 * @method Integer[] findBy(IQuery $query)
 * @method Integer[] findCountBy(IQuery $query)
 */
class IntegerRepository extends Repository
{

}
