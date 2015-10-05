<?php

namespace Joseki\LeanMapper\ClosureTable;

use Joseki\LeanMapper\BaseEntity;

class Node
{

    /** @var  IClosureEntity */
    public $data;

    /** @var  BaseEntity */
    public $parent;

    /** @var  Node[] */
    public $children;



    /**
     * @param IClosureEntity $data
     * @param Node[] $children
     */
    function __construct(IClosureEntity $data, $children)
    {
        $this->children = $children;
        $this->data = $data->descendant;
        $this->parent = $data->ancestor;
    }



    /**
     * @return bool
     */
    public function isList()
    {
        return count($this->children) === 0;
    }
}
