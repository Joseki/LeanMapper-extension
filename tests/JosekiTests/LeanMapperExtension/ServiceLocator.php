<?php

namespace Joseki\Tests;

include __DIR__ . '/TestMapper.php';

use JosekiTests\Mapper\TestMapper;
use LeanMapper\Connection;



class ServiceLocator
{
	/** @var  Connection */
	private static $connection;

	/** @var  TestMapper */
	private static $mapper;



	public static function getConnection()
	{
		if (!self::$connection) {
			self::$connection = new Connection(array(
				'driver' => 'sqlite3',
				'database' => __DIR__ . '/db/database.sq3',
			));
		}
		return self::$connection;
	}



	public static function getMapper()
	{
		if (!self::$mapper) {
			self::$mapper = new TestMapper(array('Modul'));
		}
		return self::$mapper;
	}
}
