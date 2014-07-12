<?php


namespace JosekiTests\LeanMapperExtension;

use Joseki\LeanMapper\Mapper;

class MapperMock extends Mapper
{

    /** @var string */
    protected $defaultEntityNamespace = 'JosekiTests\LeanMapperExtension\Tables';



    public function camelToUnderscoreMock($s)
    {
        return $this->camelToUnderscore($s);
    }



    public function underScoreToCamelMock($s)
    {
        return $this->underScoreToCamel($s);
    }
} 
