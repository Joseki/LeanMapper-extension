<?php

namespace UnitTests\Tables;

use Joseki\LeanMapper\Repository;
use LeanMapperQuery\IQuery;

/**
 * @method Permission get($id)
 * @method Permission findOneBy(IQuery $query)
 * @method Permission[] findAll($limit = null, $offset = null)
 * @method Permission[] findBy(IQuery $query)
 * @method Permission[] findCountBy(IQuery $query)
 */
class PermissionRepository extends Repository
{

}
