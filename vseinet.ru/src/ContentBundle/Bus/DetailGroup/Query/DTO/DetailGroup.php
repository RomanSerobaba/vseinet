<?php 

namespace ContentBundle\Bus\DetailGroup\Query\DTO;

use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Annotation as VIA;

class DetailGroup
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