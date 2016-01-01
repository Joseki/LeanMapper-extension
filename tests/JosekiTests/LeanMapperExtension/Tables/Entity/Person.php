<?php

namespace UnitTests\Tables;

use Joseki\LeanMapper\BaseEntity;

/**
 * @property string $id m:size(25)
 * @property Person $person1 m:hasOne(person1:)
 * @property Person|null $person2 m:hasOne(person2:)
 * @property Integer $integer m:hasOne(integer:)
 */
class Person extends BaseEntity
{

}
