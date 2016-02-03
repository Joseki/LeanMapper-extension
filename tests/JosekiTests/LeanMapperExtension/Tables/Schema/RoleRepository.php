<?php

namespace UnitTests\Tables;

use Joseki\LeanMapper\Repository;
use LeanMapperQuery\IQuery;

/**
 * @method Role   get($id)
 * @method Role   findOneBy(IQuery $query)
 * @method Role[] findAll($limit = null, $offset = null)
 * @method Role[] findBy(IQuery $query)
 * @method Role[] findCountBy(IQuery $query)
 */
class RoleRepository extends Repository
{

}
