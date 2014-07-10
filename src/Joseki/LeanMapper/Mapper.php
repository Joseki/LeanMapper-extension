<?php

namespace Joseki\LeanMapper;

use LeanMapper\DefaultMapper;
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
        return $this->camelToUnderscore($this->trimNamespace($entityClass));
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
        return $namespace . ucfirst($this->underscoreToCamel($table));
    }



    /**
     * someField -> some_field
     * @param string $entityClass
     * @param string $field
     * @return string
     */
    public function getColumn($entityClass, $field)
    {
        return $this->camelToUnderscore($field);
    }



    /**
     * some_field -> someField
     * @param string $table
     * @param string $column
     * @return string
     */
    public function getEntityField($table, $column)
    {
        return $this->underscoreToCamel($column);
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
        return $this->camelToUnderscore($this->trimNamespace($class));
    }



    /**
     * camelCase -> underdash_separated.
     * @param  string
     * @return string
     */
    protected function camelToUnderscore($s)
    {
        $s = preg_replace('#(.)(?=[A-Z])#', '$1_', $s);
        $s = strtolower($s);
        $s = rawurlencode($s);
        return $s;
    }



    /**
     * underdash_separated -> camelCase
     * @param  string
     * @return string
     */
    protected function underscoreToCamel($s)
    {
        $s = strtolower($s);
        $s = preg_replace('#_(?=[a-z])#', ' ', $s);
        $s = substr(ucwords('x' . $s), 1);
        $s = str_replace(' ', '', $s);
        return $s;
    }



    /**
     * Trims namespace part from fully qualified class name
     * Handles table prefixes from extended namespaces
     * App\Entity\User => User
     *
     * @param $class
     * @return string
     */
    protected function trimNamespace($class)
    {
        $class = ltrim($class, '\\');
        $namespaces = explode('\\', $class);
        return end($namespaces);
    }
}

