<?php

namespace UnitTests\Tables;

use Joseki\LeanMapper\BaseEntity;

/**
 * @property string $id m:size(50)
 * @property Role|null $parent m:hasOne (parent)
 */
class Role extends BaseEntity
{

}
