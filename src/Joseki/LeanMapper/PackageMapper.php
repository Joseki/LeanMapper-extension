<?php

namespace Joseki\LeanMapper;

use LeanMapper\Row;
use Nette\Utils\Strings;

class PackageMapper extends Mapper
{

    /** @var array */
    private $tableToRepository;

    /** @var array */
    private $tableToSchema;

    /** @var array */
    private $repositoryToTable;



    /**
     * @param array $tableToRepository
     * @param array $tableToSchema
     */
    public function __construct(array $tableToRepository = [], array $tableToSchema = [])
    {
        $this->tableToRepository = $tableToRepository;
        $this->tableToSchema = $tableToSchema;
        $this->repositoryToTable = array_flip($tableToRepository);
    }



    /**
     * @inheritdoc
     */
    public function getEntityClass($table, Row $row = null)
    {
        $parts = explode('.', $table);
        $table = array_pop($parts);

        $repositoryClass = $this->tableToRepository[$table];
        return substr($repositoryClass, 0, -10);
    }



    /**
     * @inheritdoc
     */
    public function getTableByRepositoryClass($repositoryClass)
    {
        if (Strings::endsWith($repositoryClass, 'ClosureRepository')) {
            $class = substr($repositoryClass, 0, -strlen('ClosureRepository')) . 'Repository';
            if (in_array('Joseki\LeanMapper\ClosureTable\ClosureRepositoryTrait', class_uses($class))) {
                $repositoryClass = $closure = $class;
            }
        }

        if (!array_key_exists($repositoryClass, $this->repositoryToTable)) {
            throw new InvalidArgumentException(sprintf('Class "%s" not registered in Mapper', $repositoryClass));
        }

        $table = $this->repositoryToTable[$repositoryClass];
        $schema = $this->tableToSchema[$table];
        if (isset($closure)) {
            $table .= '_closure';
        }
        return $schema ? implode('.', [$schema, $table]) : $table;
    }



    /**
     * @inheritdoc
     */
    public function getTable($entityClass)
    {
        return $this->getTableByRepositoryClass($entityClass . 'Repository');
    }



    /**
     * @param string $sourceTable
     * @param string $targetTable
     * @return string
     */
    public function getRelationshipColumn($sourceTable, $targetTable)
    {
        $parts = explode('.', $targetTable);
        $table = array_pop($parts);

        return $table;
    }
}
