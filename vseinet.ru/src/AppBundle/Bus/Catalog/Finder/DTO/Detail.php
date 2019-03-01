<?php

namespace AppBundle\Bus\Catalog\Finder\DTO;

use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Validator\Constraints\Enum;

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
     * @Enum("AppBundle\Enum\DetailType", choises="filter_type_codes")
     */
    public $typeCode;

    /**
     * @Assert\Type(type="string")
     */
    public $unit;

    /**
     * @Assert\All(@Assert\Type(type="AppBundle\Bus\Catalog\Finder\DTO\DetailValue"))
     */
    public $values;


    public function __construct($id, $name, $sectionId, $typeCode, $unit)
    {
        $this->id = $id;
        $this->name = $name;
        $this->sectionId = $sectionId;
        $this->typeCode = $typeCode;
        $this->unit = $unit;
    }
}
