<?php

namespace UnitTests\Tables;

use Joseki\LeanMapper\BaseEntity;
use Joseki\LeanMapper\ClosureTable\IClosureEntity;

/**
 * @property-read int $id (descendant)
 * @property Category $ancestor m:hasOne (ancestor)
 * @property Category $descendant m:hasOne (descendant)
 * @property int $depth
 */
class CategoryClosure extends BaseEntity implements IClosureEntity
{

}
