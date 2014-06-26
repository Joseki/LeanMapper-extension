<?php


namespace Joseki\LeanMapper;

use LeanMapper\Exception\InvalidArgumentException;
use LeanMapperQuery\Entity;

class BaseEntity extends Entity
{
    protected static $magicMethodsPrefixes = array('findOne', 'findCount', 'find');



    protected function find($field, EntityQuery $query)
    {
        $entities = $this->queryProperty($field, $query);
        return $this->entityFactory->createCollection($entities);
    }



    protected function findOne($field, EntityQuery $query)
    {
        $query->limit(1);
        $entities = $this->queryProperty($field, $query);
        if ($entities) {
            return $entities[0];
        }
        return null;
    }



    protected function findCount($field, EntityQuery $query)
    {
        return count($this->queryProperty($field, $query));
    }



    protected function createQueryObject($field)
    {
        return new EntityQuery($this, $field);
    }



    public function __call($name, array $arguments)
    {
        if (preg_match('#^query(.+)$#', $name, $matches)) {
            return $this->createQueryObject($matches[1]);
        } else {
            return parent::__call($name, $arguments);
        }
    }



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

