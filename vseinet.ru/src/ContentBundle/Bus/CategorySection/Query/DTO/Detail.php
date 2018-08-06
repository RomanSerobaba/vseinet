<?php 

namespace ContentBundle\Bus\CategorySection\Query\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class Detail
{
    /**
     * @Assert\Type(type="integer")
     */
    public $id;

    /**
     * @Assert\Type(type="integer")
     */
    public $groupId;

    /**
     * @Assert\Type(type="string")
     */
    public $name;

    /**
     * @Assert\Type(type="string")
     */
    public $typeCode;

    /**
     * @Assert\Type(type="boolean")
     */
    public $isRequired;

    /**
     * @Assert\Type(type="string")
     */
    public $measureUnit;

    /**
     * @Assert\Type(type="array<integer>")
     */
    public $dependIds = [];

    /**
     * @Assert\Type(type="array<integer>")
     */
    public $valueIds = [];


    public function __construct($id, $groupId, $name, $typeCode, $isRequired, $measureUnit)
    {
        $this->id = $id;
        $this->groupId = $groupId;
        $this->name = $name;
        $this->typeCode = $typeCode;
        $this->isRequired = $isRequired ? true : false;
        $this->measureUnit = $measureUnit;
    }
}