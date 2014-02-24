<?php


namespace Joseki\LeanMapper;

use DibiFluent;
use LeanMapper\Connection;
use Nette\Object;

class Query extends Object
{
	/** @var  DibiFluent */
	private $fluent;

	public function __construct(Connection $connection)
	{
		$this->fluent = $connection->command();
	}

	public function select($field)
	{
		$this->fluent->select($field);
		return $this;
	}

	public function distinct()
	{
		$this->fluent->distinct();
		return $this;
	}

	public function from($table)
	{
		$this->fluent->from($table);
		return $this;
	}

	public function join($table)
	{
		call_user_func_array(array($this->fluent, 'join'), func_get_args());
		return $this;
	}

	public function on($cond)
	{
		call_user_func_array(array($this->fluent, 'on'), func_get_args());
		return $this;
	}

	public function where($cond)
	{
		call_user_func_array(array($this->fluent, 'where'), func_get_args());
		return $this;
	}

	public function groupBy($field)
	{
		call_user_func_array(array($this->fluent, 'groupBy'), func_get_args());
		return $this;
	}

	public function having($cond)
	{
		call_user_func_array(array($this->fluent, 'having'), func_get_args());
		return $this;
	}

	public function orderBy($field)
	{
		call_user_func_array(array($this->fluent, 'orderBy'), func_get_args());
		return $this;
	}

	public function limit($limit)
	{
		$this->fluent->removeClause('limit');
		$this->fluent->limit($limit);
		return $this;
	}

	public function offset($offset)
	{
		$this->fluent->removeClause('offset');
		$this->fluent->offset($offset);
		return $this;
	}

	public function removeClause($clause)
	{
		$this->fluent->removeClause($clause);
		return $this;
	}

	public function count()
	{
		$this->fluent->count();
		return $this;
	}

	/**
	 * Exports current state
	 *
	 * @param string|null $clause
	 * @param array|null $args
	 * @return string
	 */
	public function _export($clause = NULL, $args = NULL)
	{
		return $this->fluent->_export($clause, $args);
	}

}

class InvalidStateException extends \LogicException
{

}
