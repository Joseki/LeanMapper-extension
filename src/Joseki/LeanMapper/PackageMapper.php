<?php

namespace Joseki\LeanMapper;

use LeanMapper\Row;

class PackageMapper extends Mapper
{

    /** @var array */
    private $tables = [];

    /** @var array */
    private $repositories = [];



    /**
     * @param array $tables
     */
    public function __construct(array $tables = array())
    {
        $this->tables = $tables;
        $this->repositories = array_flip($tables);
    }



    /*
     * @inheritdoc
     */
    public function getEntityClass($table, Row $row = null)
    {
        $repositoryClass = $this->tables[$table];
        return substr($repositoryClass, 0, -10);
    }
}
