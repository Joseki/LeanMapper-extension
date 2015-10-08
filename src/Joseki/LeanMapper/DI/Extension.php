<?php

namespace Joseki\LeanMapper\DI;

use Nette;

class Extension extends Nette\DI\CompilerExtension
{

    public $defaults = [
        'packages' => [],
        'db' => [],
        'namespace' => '',
        'profiler' => true,
        'logFile' => null
    ];



    public function loadConfiguration()
    {
        $container = $this->getContainerBuilder();
        $config = $this->getConfig($this->defaults);

        list($packages, $tables) = $this->findTablePackages($config);
        foreach ($tables as $table => $package) {
            $table = ucfirst($this->underscoreToCamel($table));
            $container->addDefinition($this->prefix('table.' . $table))
                ->setClass($config['namespace'] . '\\' . $package . '\\' . $table . 'Repository');
        }

        $container->addDefinition($this->prefix('mapper'))
            ->setClass('Joseki\LeanMapper\PackageMapper', array($packages, $tables, $config['namespace']));

        $container->addDefinition($this->prefix('entityFactory'))
            ->setClass('LeanMapper\DefaultEntityFactory');

        $connection = $container->addDefinition($this->prefix('connection'))
            ->setClass('LeanMapper\Connection', array($config['db']));

        if (isset($config['db']['flags'])) {
            $flags = 0;
            foreach ((array)$config['db']['flags'] as $flag) {
                $flags |= constant($flag);
            }
            $config['db']['flags'] = $flags;
        }

        if (class_exists('Tracy\Debugger') && $container->parameters['debugMode'] && $config['profiler']) {
            $panel = $container->addDefinition($this->prefix('panel'))->setClass('Dibi\Bridges\Tracy\Panel');
            $connection->addSetup(array($panel, 'register'), array($connection));
            if ($config['logFile']) {
                $fileLogger = $container->addDefinition($this->prefix('fileLogger'))->setClass('SavingFunds\LeanMapper\FileLogger');
                $connection->addSetup(array($fileLogger, 'register'), array($connection, $config['logFile']));
            }
        }
    }



    private function findTablePackages($config)
    {
        $packages = [];
        $tables = [];
        $this->parsePackages($packages, $tables, $config['packages']);
        return array($packages, $tables);
    }



    private function underscoreToCamel($s)
    {
        $s = strtolower($s);
        $s = preg_replace('#_(?=[a-z])#', ' ', $s);
        $s = substr(ucwords('x' . $s), 1);
        $s = str_replace(' ', '', $s);
        return $s;
    }



    private function parsePackages(&$packages, &$tables, $data, $package = '')
    {
        foreach ($data as $prefix => $table) {
            if (is_string($table)) {
                if (!isset($packages[$package])) {
                    $packages[$package] = [];
                }
                $packages[$package][] = $table;
                if (array_key_exists($table, $tables)) {
                    throw new \Exception("Multiple packages for table $table found.");
                }
                $tables[$table] = $package;
            } elseif (is_array($table)) {
                $this->parsePackages($packages, $tables, $table, trim("$package\\$prefix", '\\'));
            }
        }
    }

}
