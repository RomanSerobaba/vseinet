<?php 

namespace ContentBundle\Bus\Detail\Query\DTO;

use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Annotation as VIA;

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
    public $groupId;

    /**
     * @Assert\Type(type="integer")
     */
    public $sectionId;

    /**
     * @Assert\Type(type="string")
     */
    public $typeCode;

    /**
     * @Assert\Type(type="integer")
     */
    public $measureId;

    /**
     * @Assert\Type(type="integer")
     */
    public $unitId;


    public function __construct($id, $name, $groupId, $sectionId, $typeCode, $measureId, $unitId)
    {
        $this->id = $id;
        $this->name = $name;
        $this->groupId = $groupId;
        $this->sectionId = $sectionId;
        $this->typeCode = $typeCode;
        $this->measureId = $measureId;
        $this->unitId = $unitId;
    }
}