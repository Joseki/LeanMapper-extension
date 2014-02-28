<?php

namespace Joseki\LeanMapper;

use LeanMapper\DefaultMapper;
use LeanMapper\Row;



/**
 * Standard mapper for conventions:
 * - underdash separated names of tables and cols
 * - PK and FK is in [destinationtable] format
 * - entity repository is named [Entity[\SubModule]]Repository
 * - M:N relations are stored in [table1]_[table2] tables
 *
 * @author Jan Nedbal
 * @author Miroslav Paulík
 */
class Mapper extends DefaultMapper
{
	/** @var string */
	protected $defaultEntityNamespace = 'App\\Tables';

	/** @var  array */
	public $predefinedPrefixes;



	function __construct($predefinedPrefixes = array())
	{
		$this->predefinedPrefixes = $predefinedPrefixes;
	}



	/**
	 * @param string $prefix
	 */
	public function addTablePrefix($prefix)
	{
		if (!in_array($prefix, $this->predefinedPrefixes)) {
			$this->predefinedPrefixes[] = $prefix;
		}
	}



	/**
	 * App\Entity\SomeEntity -> some_entity
	 * @param string $entityClass
	 * @return string
	 */
	public function getTable($entityClass)
	{
		return $this->camelToUnderdash($this->trimNamespace($entityClass));
	}



	/**
	 * some_entity -> App\Entity\SomeEntity
	 * some_entity -> App\Entity\Some\Entity if 'some' is a predefined prefix
	 * @param string $table
	 * @param Row $row
	 * @return string
	 */
	public function getEntityClass($table, Row $row = NULL)
	{
		$namespace = $this->defaultEntityNamespace . '\\';
		$len = strlen($table);
		foreach ($this->predefinedPrefixes as $prefix) {
			$prefix = strtolower($prefix);
			if ($len > strlen($prefix) && $prefix . '_' == substr($table, 0, strlen($prefix) + 1)) {
				$namespace .= ucfirst($this->underdashToCamel($prefix)) . '\\';
				$table = substr($table, strlen($prefix) + 1);
				break;
			}
		}
		return $namespace . ucfirst($this->underdashToCamel($table));
	}



	/**
	 * someField -> some_field
	 * @param string $entityClass
	 * @param string $field
	 * @return string
	 */
	public function getColumn($entityClass, $field)
	{
		return $this->camelToUnderdash($field);
	}



	/**
	 * some_field -> someField
	 * @param string $table
	 * @param string $column
	 * @return string
	 */
	public function getEntityField($table, $column)
	{
		return $this->underdashToCamel($column);
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
	 * App\Repository\Module\SomeEntityRepository -> module_some_entity
	 * @param string $repositoryClass
	 * @return string
	 */
	public function getTableByRepositoryClass($repositoryClass)
	{
		$class = preg_replace('#([a-z0-9]+)Repository$#', '$1', $repositoryClass);
		return $this->camelToUnderdash($this->trimNamespace($class));
	}



	/**
	 * camelCase -> underdash_separated.
	 * @param  string
	 * @return string
	 */
	protected function camelToUnderdash($s)
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
	protected function underdashToCamel($s)
	{
		$s = strtolower($s);
		$s = preg_replace('#_(?=[a-z])#', ' ', $s);
		$s = substr(ucwords('x' . $s), 1);
		$s = str_replace(' ', '', $s);
		return $s;
	}



	/**
	 * Trims default namespace part from fully qualified class name
	 * Joins remaining namespaces (from predefined prefixes) with classname into a single name
	 * [\]App\Entity\User => User
	 * [\]App\Entity\Module\User => ModuleUser
	 *
	 * @param $class
	 * @return string
	 */
	protected function trimNamespace($class)
	{
		$class = ltrim($class, '\\');
		$namespaces = explode('\\', $class);
		$defaultNamespaces = explode('\\', $this->defaultEntityNamespace);

		if (count($namespaces) > count($defaultNamespaces) + 1) {
			$namespaces = array_slice($namespaces, count($defaultNamespaces));
			return implode("", $namespaces);
		} else {
			return end($namespaces);
		}
	}

}

