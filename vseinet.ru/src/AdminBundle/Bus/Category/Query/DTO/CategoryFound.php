<?php 

namespace AdminBundle\Bus\Category\Query\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class CategoryFound
{
    /**
     * @Assert\Type(type="integer")
     */
    public $id;

    /**
     * @Assert\Type(type="string")
     */
    public $name;

    /**
     * @Assert\Type(type="integer")
     */
    public $pid;

    /**
     * @Assert\Type(type="array<string>")
     */
    public $path;


    public function __construct($id, $name, $pid)
    {
        $this->id = $id;
        $this->name = $name;
        $this->pid = $pid;
    }
}
