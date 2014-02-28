<?php

namespace Joseki\LeanMapper;

use LeanMapper\Connection;
use LeanMapper\Entity;
use LeanMapper\Exception\InvalidArgumentException;
use LeanMapper\IMapper;
use LeanMapper\Repository as LR;



/**
 *
 * Base repository with Query Object support
 *
 * @author Miroslav Paulík
 *
 * @property array $onBeforePersist
 * @property array $onBeforeCreate
 * @property array $onBeforeUpdate
 * @property array $onBeforeDelete
 * @property array $onAfterPersist
 * @property array $onAfterCreate
 * @property array $onAfterUpdate
 * @property array $onAfterDelete
 */
abstract class Repository extends LR
{

	/**
	 * @param Connection $connection
	 * @param IMapper $mapper
	 */
	public function __construct(Connection $connection, IMapper $mapper)
	{
		parent::__construct($connection, $mapper);
	}



	/**
	 * @return Query
	 */
	public function createQueryObject()
	{
		$query = new Query($this->connection);
		$query->select('*')->from($this->getTable());
		return $query;
	}



	/**
	 * @param Query $query
	 * @return \DibiResult|int
	 */
	protected function prepare(Query $query)
	{
		return $this->connection->query($this->translate($query));
	}



	/**
	 * @param Query $query
	 * @return string
	 */
	private function translate(Query $query)
	{
		$translated = $this->connection->translate($query->_export());

		/** @var Entity $entityClass */
		$entityClass = $this->mapper->getEntityClass($this->getTable());
		$reflection = $entityClass::getReflection($this->mapper);
		foreach ($reflection->getEntityProperties() as $property) {
			$name = $property->getName();
			$column = $property->getColumn();
			$translated = preg_replace("/`$name`/", "`$column`", $translated);
		}

		return $translated;
	}



	/**
	 * @param $query
	 * @return array|mixed
	 * @throws InvalidArgumentException
	 * @throws NotFoundException
	 */
	public function findBy($query)
	{
		if (is_array($query)) {
			/** @var Query $query */
			$query = $this->createQueryObject()->where($query);
		} elseif (!$query instanceof Query) {
			throw new InvalidArgumentException;
		}

		$limit = $query->_export('limit');
		if (is_array($limit) && $limit[1] === 1) {
			$row = $this->prepare($query)->fetch();
			if ($row === FALSE) {
				throw new NotFoundException('Entity not found.');
			}
			return $this->createEntity($row);
		}

		return $this->createEntities($this->prepare($query)->fetchAll());
	}



	/**
	 * @param null $limit
	 * @param null $offset
	 * @return array
	 */
	public function findAll($limit = NULL, $offset = NULL)
	{
		$query = $this->createQueryObject();
		if ($limit) {
			$query->limit($limit);
		}
		if ($offset) {
			$query->offset($offset);
		}
		return $this->findBy($query);
	}



	/**
	 * @param Query|array $query
	 * @throws InvalidArgumentException
	 * @return array|mixed
	 */
	public function findOneBy($query)
	{
		if (is_array($query)) {
			$query = $this->createQueryObject()->where($query);
		} elseif (!$query instanceof Query) {
			throw new InvalidArgumentException;
		}
		$query->limit(1);
		return $this->findBy($query);
	}



	/**
	 * @param $id
	 * @throws NotFoundException
	 * @return BaseEntity
	 */
	public function get($id)
	{
		$query = $this->createQueryObject();
		$query->where("id = %s", $id);
		return $this->findOneBy($query);
	}

}
