<?php

namespace Joseki\LeanMapper\DI;

use Joseki\LeanMapper\Repository;
use Joseki\LeanMapper\Utils;
use Nette;
use Nette\Loaders\RobotLoader;
use Nette\Utils\Strings;

class Extension extends Nette\DI\CompilerExtension
{

    public $defaults = [
        'db' => [],
        'namespace' => '',
        'profiler' => true,
        'logFile' => null,
        'scanDirs' => null,
        'map' => [],
        'defaultSchema' => null,
        'schemaMap' => [],
    ];



    public function loadConfiguration()
    {
        $container = $this->getContainerBuilder();
        $this->defaults['scanDirs'] = $container->expand('%appDir%');
        $config = $this->getConfig($this->defaults);

        $this->validateRepositories($config['map']);
        foreach ($config['schemaMap'] as $schemaRepositories) {
            $this->validateRepositories($schemaRepositories);
        }

        $tables = $this->mergeTables($this->findRepositories($config), $config['map']);
        $tables = $this->mapSchemas($tables, $config['schemaMap'], $config['defaultSchema']);

        foreach ($tables as $table => $repositoryClass) {
            $container->addDefinition($this->prefix('table.' . $table))
                ->setClass($repositoryClass);
        }

        $container->addDefinition($this->prefix('mapper'))
            ->setClass('Joseki\LeanMapper\PackageMapper', [$tables]);

        $container->addDefinition($this->prefix('entityFactory'))
            ->setClass('LeanMapper\DefaultEntityFactory');

        $connection = $container->addDefinition($this->prefix('connection'))
            ->setClass('LeanMapper\Connection', [$config['db']]);

        if (isset($config['db']['flags'])) {
            $flags = 0;
            foreach ((array)$config['db']['flags'] as $flag) {
                $flags |= constant($flag);
            }
            $config['db']['flags'] = $flags;
        }

        if (class_exists('Tracy\Debugger') && $container->parameters['debugMode'] && $config['profiler']) {
            $panel = $container->addDefinition($this->prefix('panel'))->setClass('Dibi\Bridges\Tracy\Panel');
            $connection->addSetup([$panel, 'register'], [$connection]);
            if ($config['logFile']) {
                $fileLogger = $container->addDefinition($this->prefix('fileLogger'))->setClass('SavingFunds\LeanMapper\FileLogger');
                $connection->addSetup([$fileLogger, 'register'], [$connection, $config['logFile']]);
            }
        }
    }



    private function findRepositories($config)
    {
        $classes = [];

        if ($config['scanDirs']) {
            $robot = new RobotLoader;
            $robot->setCacheStorage(new Nette\Caching\Storages\DevNullStorage);
            $robot->addDirectory($config['scanDirs']);
            $robot->acceptFiles = '*.php';
            $robot->rebuild();
            $classes = array_keys($robot->getIndexedClasses());
        }

        $repositories = [];
        foreach (array_unique($classes) as $class) {
            if (class_exists($class)
                && ($rc = new \ReflectionClass($class)) && $rc->isSubclassOf('Joseki\LeanMapper\Repository')
                && !$rc->isAbstract()
            ) {
                $repositoryClass = $rc->getName();
                $entityClass = Strings::endsWith($repositoryClass, 'Repository') ? substr(
                    $repositoryClass,
                    0,
                    strlen($repositoryClass) - 10
                ) : $repositoryClass;
                $table = Utils::camelToUnderscore(Utils::trimNamespace($entityClass));
                if (array_key_exists($table, $repositories)) {
                    throw new \Exception(sprintf('Multiple repositories for table %s found.', $table));
                }
                $repositories[$table] = $repositoryClass;
            }
        }
        return $repositories;
    }



    private function mapSchemas(array $tables, array $schemas, $defaultSchema)
    {
        if (!$defaultSchema) {
            return $tables;
        }

        /** @var array $foundTables REPOSITORY => TABLE */
        $foundTables = array_flip($tables);
        /** @var array $pairs REPOSITORY => SCHEMA */
        $pairs = array_fill_keys(array_values($tables), $defaultSchema);

        foreach ($schemas as $name => $repositories) {
            foreach ($repositories as $repository) {
                $pairs[$repository] = $name;
            }
        }

        $result = [];
        foreach ($foundTables as $repository => $table) {
            $newTable = sprintf('%s.%s', $pairs[$repository], $table);
            $result[$newTable] = $repository;
        }
        return $result;
    }



    private function mergeTables($foundTables, $definedTables)
    {
        $foundTables = array_flip($foundTables);
        $definedTables = array_flip($definedTables);
        return array_flip(array_merge($foundTables, $definedTables));
    }



    private function validateRepositories(array $classes)
    {
        foreach ($classes as $class) {
            if (!$class instanceof Repository) {
                return false;
            }
        }
        return true;
    }
}
