<?php

namespace JosekiTests\LeanMapperExtension;

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
            self::$connection = new Connection(array(
                'driver' => 'mysql',
                'database' => 'testing',
                'username' => 'travis',
                'host' => '127.0.0.1'
            ));
        }
        return self::$connection;
    }



    public static function getMapper()
    {
        if (!self::$mapper) {
            self::$mapper = new MapperMock(array('Special', 'camelCase'));
        }
        return self::$mapper;
    }
}
