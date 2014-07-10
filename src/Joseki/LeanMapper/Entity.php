<?php


namespace Joseki\LeanMapper;

use LeanMapper\Exception\InvalidArgumentException;
use LeanMapper\Relationship\HasOne;
use LeanMapperQuery\Entity;

class BaseEntity extends Entity
{
    protected static $magicMethodsPrefixes = array('findOneBy', 'findCountBy', 'findBy');



    protected function findBy($field, EntityQuery $query)
    {
        $entities = $this->queryProperty($field, $query);
        return $this->entityFactory->createCollection($entities);
    }



    protected function findOneBy($field, EntityQuery $query)
    {
        $query->limit(1);
        $entities = $this->queryProperty($field, $query);
        if ($entities) {
            return $entities[0];
        }
        return null;
    }



    protected function findCountBy($field, EntityQuery $query)
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



    public function __set($name, $value)
    {
        $property = $this->getCurrentReflection()->getEntityProperty($name);
        $relationship = $property->getRelationship();
        if (($relationship instanceof HasOne) and !($value instanceof \LeanMapper\Entity)) {
            if (is_string($value) and ctype_digit($value)) {
                settype($value, 'integer');
            }
            $this->row->{$property->getColumn()} = $value;
            $this->row->cleanReferencedRowsCache(
                $relationship->getTargetTable(),
                $relationship->getColumnReferencingTargetTable()
            );
        } else {
            parent::__set($name, $value);
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

