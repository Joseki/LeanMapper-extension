<?php


namespace Joseki\LeanMapper\ClosureTable;

use Joseki\LeanMapper\BaseEntity;

/**
 * @property BaseEntity $ancestor m:hasOne (ancestor)
 * @property BaseEntity $descendant (descendant)
 * @property int $depth
 */
interface IClosureEntity {

} 
