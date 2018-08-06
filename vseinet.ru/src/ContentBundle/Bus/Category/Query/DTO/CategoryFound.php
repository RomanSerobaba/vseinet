<?php 

namespace ContentBundle\Bus\Category\Query\DTO;

use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Annotation as VIA;

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
    

    public function __construct($id, $name, $pid)
    {
        $this->id = $id;
        $this->name = $name;
        $this->pid = $pid;
    }
}