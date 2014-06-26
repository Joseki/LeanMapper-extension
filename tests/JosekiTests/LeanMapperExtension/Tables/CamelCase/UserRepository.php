<?php

namespace JosekiTests\LeanMapperExtension\Tables\CamelCase;

use Joseki\LeanMapper\Repository;

/**
 * @method User get($id)
 * @method User findOneBy($condition)
 * @method User[] findAll($limit = null, $offset = null)
 * @method User[] findBy($condition)
 */
class UserRepository extends Repository
{

}




