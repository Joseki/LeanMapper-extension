<?php

namespace JosekiTests\LeanMapperExtension\Tables;

use Joseki\LeanMapper\Repository;
use LeanMapperQuery\IQuery;

/**
 * @method User get($id)
 * @method User findOneBy(IQuery $query)
 * @method User[] findAll($limit = null, $offset = null)
 * @method User[] findBy(IQuery $query)
 * @method User[] findCountBy(IQuery $query)
 */
class UserRepository extends Repository
{

}
