<?php 

namespace ContentBundle\Bus\Task\Query\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class Task
{
    /**
     * @Assert\Type(type="integer")
     */
    public $id;

    /**
     * @Assert\Type(type="integer")
     */
    public $categoryId;

    /**
     * @Assert\Type(type="string")
     */
    public $path;

    /**
     * @Assert\Type(type="string")
     */
    public $name;


    public function __construct($id, $categoryId, $path, $name) 
    {
        $this->id = $id;
        $this->categoryId = $categoryId;
        $this->path = $path;
        $this->name = $name;
    }
}
