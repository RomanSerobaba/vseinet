<?php 

namespace AppBundle\Bus\Catalog\Query\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class Detail
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
    public $valueId;

    /**
     * @Assert\type(type="string")
     */
    public $valueName;


    public function __construct($id, $name, $valueId, $valueName) {
        $this->id = $id;
        $this->name = $name;
        $this->valueId = $valueId;
        $this->valueName = $valueName;
    }
}
