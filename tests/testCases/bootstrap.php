<?php

require __DIR__ . '/../../libs/composer/autoload.php';

if (!class_exists('Tester\Assert')) {
	echo "Install Nette Tester using `composer update --dev`\n";
	exit(1);
}

function id($val)
{
	return $val;
}

$configurator = new Nette\Configurator;
$configurator->setDebugMode(TRUE);
//$configurator->setDebugMode(FALSE);
$configurator->enableDebugger(__DIR__ . '/temp');
$configurator->setTempDirectory(__DIR__ . '/temp');
$configurator->createRobotLoader()
	->addDirectory(__DIR__ . '/../../src')
	->addDirectory(__DIR__ . '/classes')
	->register();

$configurator->addConfig(__DIR__ . '/config/config.neon');
$container = $configurator->createContainer();

return $container;
