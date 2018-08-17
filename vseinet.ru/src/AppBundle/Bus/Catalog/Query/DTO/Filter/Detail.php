<?php 

namespace AppBundle\Bus\Catalog\Query\DTO\Filter;

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
    public $sectionId;

    /**
     * @Assert\Choice({"number", "enum", "boolean"}, strict=true)
     */
    public $typeCode;

    /**
     * @Assert\Type(type="string")
     */
    public $unit;

    /**
     * @Assert\Type(type="array")
     */
    public $values = [];


    public function __construct($id, $name, $sectionId, $typeCode, $unit)
    {
        $this->id = $id;
        $this->name = $name;
        $this->sectionId = $sectionId;
        $this->typeCode = $typeCode;
        $this->unit = $unit;
    }
}
