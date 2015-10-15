<?php

namespace Joseki\LeanMapper;

use LeanMapper\Row;

class PackageMapper extends Mapper
{

    /** @var array */
    private $packages = [];

    /** @var array */
    private $tables = [];



    /**
     * @param array $tables
     */
    public function __construct(array $tables = array())
    {
        $this->tables = $tables;

        foreach ($this->tables as $table => $package) {
            if (!array_key_exists($package, $this->packages)) {
                $this->packages[$package] = [];
            }
            $this->packages[$package][] = $table;
        }
    }



    /*
     * @inheritdoc
     */
    public function getEntityClass($table, Row $row = null)
    {
        $tablePackage = $this->tables[$table];
        $table = ucfirst($this->underscoreToCamel($table));
        return ltrim(sprintf('%s\%s', $tablePackage, $table), '\\');
    }
}
