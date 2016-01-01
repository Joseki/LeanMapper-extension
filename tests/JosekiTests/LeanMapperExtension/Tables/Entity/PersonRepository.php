<?php

namespace UnitTests\Tables;

use Joseki\LeanMapper\Repository;
use LeanMapperQuery\IQuery;

/**
 * @method Person get($id)
 * @method Person findOneBy(IQuery $query)
 * @method Person[] findAll($limit = null, $offset = null)
 * @method Person[] findBy(IQuery $query)
 * @method Person[] findCountBy(IQuery $query)
 */
class PersonRepository extends Repository
{

}
