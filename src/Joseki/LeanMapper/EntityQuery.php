<?php

namespace Joseki\LeanMapper;

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



    public function findBy()
    {
        return $this->entity->{'findBy' . ucfirst($this->field)}($this);
    }



    public function findOneBy()
    {
        return $this->entity->{'findOneBy' . ucfirst($this->field)}($this);
    }



    public function count()
    {
        return $this->entity->{'findCountBy' . ucfirst($this->field)}($this);
    }

}
