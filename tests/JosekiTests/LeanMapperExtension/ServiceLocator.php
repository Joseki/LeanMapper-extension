<?php

namespace Joseki\Tests;

use Joseki\LeanMapper\Mapper;
use LeanMapper\Connection;



class ServiceLocator
{
	/** @var  Connection */
	private static $connection;

	/** @var  Mapper */
	private static $mapper;



	public static function getConnection()
	{
		if (!self::$connection) {
			self::$connection = new Connection(array());
		}
		return self::$connection;
	}



	public static function getMapper()
	{
		if (!self::$mapper) {
			self::$mapper = new Mapper(array('Fakturace', 'dane'));
		}
		return self::$mapper;
	}
}
