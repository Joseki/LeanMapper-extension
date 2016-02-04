<?php

namespace JosekiTests\LeanMapperExtension;

use Tester\Assert;
use Tester\TestCase;
use UnitTests\Tables\Person;

$container = require __DIR__ . '/../bootstrap.php';

class EntitySetterTest extends TestCase
{

    public function testDetachedSetter()
    {
        Assert::noError(
            function () {
                $person = new Person();
                $person->id = 'name';
                $person->person1 = 'John';
                $person->person1 = null;
                $person->integer = 5;
            }
        );
    }
}

run(new EntitySetterTest());
