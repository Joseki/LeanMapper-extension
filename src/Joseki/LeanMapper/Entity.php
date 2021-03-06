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
        if ($property && $property->hasRelationship() && $property->getRelationship() instanceof HasOne && !$value instanceof \LeanMapper\Entity) {
            $relationship = $property->getRelationship();
            $targetEntityClass = $property->getType();

            /** @var Entity $targetEntity */
            $targetEntity = new $targetEntityClass;
            $targetPrimaryKeyProperty = $targetEntity->getReflection()->getEntityProperty('id');

            if ($targetPrimaryKeyProperty->isBasicType()) {
                $type = $targetPrimaryKeyProperty->getType();
                if (in_array($type, ['integer', 'float']) && is_string($value) && ctype_digit($value)) {
                    settype($value, $type);
                }
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

