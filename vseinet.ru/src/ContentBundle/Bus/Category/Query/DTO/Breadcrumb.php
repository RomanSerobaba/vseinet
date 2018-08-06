<?php 

namespace ContentBundle\Bus\Category\Query\DTO;

use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Annotation as VIA;

class Breadcrumb
{
    /**
     * @Assert\Type(type="integer")
     */
    public $id;

    /**
     * @Assert\Type(type="string")
     */
    public $name;


    public function __construct($id, $name)
    {
        $this->id = $id;
        $this->name = $name;
    }
}