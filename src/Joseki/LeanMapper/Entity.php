<?php


namespace Joseki\LeanMapper;

use LeanMapper\Entity;
use LeanMapper\Exception\InvalidArgumentException;



/**
 * @property int $id
 */
class BaseEntity extends Entity
{

	public function getEnumValues($propertyName)
	{
		$property = $this->getCurrentReflection()->getEntityProperty($propertyName);
		if (!$property->containsEnumeration()) {
			throw new InvalidArgumentException;
		}

		$values = array();
		foreach ($property->getEnumValues() as $possibleValue) {
			$values[$possibleValue] = $possibleValue;
		}

		return $values;
	}
}



class NotFoundException extends \LogicException
{

}

