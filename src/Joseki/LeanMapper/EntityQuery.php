<?php

namespace Joseki\LeanMapper;

use LeanMapperQuery\Query;
use LeanMapperQuery\Entity;

class EntityQuery extends Query implements \Countable
{

    /** @var Entity */
    private $entity;

    /** @var string */
    private $field;



    public function __construct(Entity $entity, $field)
    {
        $this->entity = $entity;
        $this->field = $field;
    }



    public function find()
    {
        return $this->entity->{'find' . ucfirst($this->field)}($this);
    }



    public function findOne()
    {
        return $this->entity->{'findOne' . ucfirst($this->field)}($this);
    }



    public function count()
    {
        return $this->entity->{'findCount' . ucfirst($this->field)}($this);
    }

}
