<?php

namespace Joseki\LeanMapper;

use LeanMapper\Row;

class PackageMapper extends Mapper
{

    /** @var string */
    protected $basePackagesNamespace;

    /** @var array */
    private $packages;

    /** @var array */
    private $tables;



    /**
     * @param array $packages
     * @param array $tables
     * @param $basePackagesNamespace
     */
    public function __construct(array $packages, array $tables, $basePackagesNamespace = '')
    {
        $this->packages = $packages;
        $this->tables = $tables;
        $this->basePackagesNamespace = $basePackagesNamespace;
    }



    /*
     * @inheritdoc
     */
    public function getEntityClass($table, Row $row = null)
    {
        $tablePackage = $this->tables[$table];
        $table = ucfirst($this->underscoreToCamel($table));
        return ltrim($this->basePackagesNamespace . '\\' . $tablePackage . '\\' . $table, '\\');
    }
}
