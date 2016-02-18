<?php

namespace Joseki\LeanMapper;

use LeanMapper\DefaultMapper;
use LeanMapper\Entity;
use LeanMapper\Row;

/**
 * Standard mapper for conventions:
 * - underscore separated names of tables and cols
 * - PK is in id format
 * - FK is in [table] format
 * - entity repository is named [Entity]Repository
 * - M:N relations are stored in [table1]_[table2] tables
 *
 * @author Jan Nedbal
 * @author Miroslav Paulík
 */
class Mapper extends DefaultMapper
{
    /** @var string */
    protected $defaultEntityNamespace = 'App\\Tables';



    /**
     * App\Entity\SomeEntity -> some_entity
     * @param string $entityClass
     * @return string
     */
    public function getTable($entityClass)
    {
        return Utils::camelToUnderscore($this->trimNamespace($entityClass));
    }



    /**
     * some_entity -> App\Entity\SomeEntity
     * @param string $table
     * @param Row $row
     * @return string
     */
    public function getEntityClass($table, Row $row = null)
    {
        $namespace = $this->defaultEntityNamespace . '\\';
        return $namespace . ucfirst(Utils::underscoreToCamel($table));
    }



    /**
     * someField -> some_field
     * @param string $entityClass
     * @param string $field
     * @return string
     */
    public function getColumn($entityClass, $field)
    {
        return Utils::camelToUnderscore($field);
    }



    /**
     * some_field -> someField
     * @param string $table
     * @param string $column
     * @return string
     */
    public function getEntityField($table, $column)
    {
        $class = $this->getEntityClass($table);
        /** @var Entity $entity */
        $entity = new $class;
        $reflection = $entity->getReflection($this);
        foreach ($reflection->getEntityProperties() as $property) {
            if ($property->getColumn() == $column) {
                return Utils::underscoreToCamel($property->getName());
            }
        }
        throw new InvalidArgumentException(sprintf("Could not find property for table '%s' and column '%s'", $table, $column));
    }



    /**
     * @param string $sourceTable
     * @param string $targetTable
     * @return string
     */
    public function getRelationshipColumn($sourceTable, $targetTable)
    {
        return $targetTable;
    }



    /**
     * App\Repository\SomeEntityRepository -> some_entity
     * @param string $repositoryClass
     * @return string
     */
    public function getTableByRepositoryClass($repositoryClass)
    {
        $class = preg_replace('#([a-z0-9]+)Repository$#', '$1', $repositoryClass);
        return Utils::camelToUnderscore($this->trimNamespace($class));
    }

}

