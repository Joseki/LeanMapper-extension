<?php

namespace JosekiTests\LeanMapperExtension\Tables;

use Joseki\LeanMapper\BaseEntity;

/**
 * @property int $id
 * @property string $firstName (my_name) m:size(25)
 * @property string $address (myaddress)
 */
class User extends BaseEntity
{

}
