<?php

namespace Joseki\LeanMapper;

class FileLogger
{

    public function register(\Dibi\Connection $connection, $path)
    {
        $connection->onEvent[] = array(
            new \Dibi\Loggers\FileLogger($path, \Dibi\Event::QUERY),
            'logEvent'
        );
    }
}
