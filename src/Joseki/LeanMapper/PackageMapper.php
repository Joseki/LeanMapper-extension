<?php

namespace Joseki\LeanMapper;

use LeanMapper\Row;
use Nette\Utils\Strings;

class PackageMapper extends Mapper
{

    /** @var array */
    protected $tableToRepository;

    /** @var array */
    protected $tableToSchema;

    /** @var array */
    protected $repositoryToTable;

    /** @var null */
    protected $defaultSchema;



    public function registerTable($table, $repositoryClass, $schema = null)
    {
        $this->tableToRepository[$table] = $repositoryClass;
        $this->tableToSchema[$table] = $schema ?: $this->defaultSchema;
        $this->repositoryToTable[$repositoryClass] = $table;
    }



    /**
     * @inheritdoc
     */
    public function getEntityClass($table, Row $row = null)
    {
        $table = Utils::trimTableSchema($table);

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
        return Utils::trimTableSchema($targetTable);
    }



    /*
	 * @inheritdoc
	 */
    public function getRelationshipTable($sourceTable, $targetTable)
    {
        $relationshipTable = sprintf(
            '%s%s%s',
            Utils::trimTableSchema($sourceTable),
            $this->relationshipTableGlue,
            Utils::trimTableSchema($targetTable)
        );

        return $this->defaultSchema ? sprintf('%s.%s', $this->defaultSchema, $relationshipTable) : $relationshipTable;
    }



    /**
     * @param null $defaultSchema
     */
    public function setDefaultSchema($defaultSchema)
    {
        $this->defaultSchema = $defaultSchema;
    }



    /**
     * @return null
     */
    public function getDefaultSchema()
    {
        return $this->defaultSchema;
    }

}
