<?php

namespace JosekiTests\Migration;

use Nette\Configurator;
use Nette\Utils\Random;
use Tester\Assert;

require_once __DIR__ . '/../bootstrap.php';

/**
 * @testCase
 */
class DIExtensionTest extends \Tester\TestCase
{

    private function prepareConfigurator()
    {
        $configurator = new Configurator;
        $configurator->setTempDirectory(TEMP_DIR);
        $configurator->addParameters(array('container' => array('class' => 'SystemContainer_' . Random::generate())));

        $configurator->addConfig(__DIR__ . '/config/config.local.neon', $configurator::NONE);

        return $configurator;
    }



    public function testRepository()
    {
        $configurator = $this->prepareConfigurator();
        $configurator->addConfig(__DIR__ . '/config/config.leanmapper.1.neon', $configurator::NONE);

        /** @var \Nette\DI\Container $container */
        $container = $configurator->createContainer();

        Assert::equal(2, count($container->findByType('Joseki\LeanMapper\Repository')));
    }
}

\run(new DIExtensionTest());
