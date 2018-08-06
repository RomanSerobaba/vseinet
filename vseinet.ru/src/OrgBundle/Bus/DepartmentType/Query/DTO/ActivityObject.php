<?php 

namespace OrgBundle\Bus\DepartmentType\Query\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class ActivityObject
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
     * @Assert\Type(type="string")
     */
    public $code;

    /**
     * @Assert\Type(type="boolean")
     */
    public $canBeFilteredByCategory;

    /**
     * @Assert\Type(type="boolean")
     */
    public $hasInterval;

    /**
     * @Assert\Type(type="boolean")
     */
    public $isNegative;

    /**
     * @var \OrgBundle\Entity\ActivityIndex[]
     *
     * @Assert\All({
     *      @Assert\Type(type="OrgBundle\Entity\ActivityIndex")
     * })
     */
    public $indexes;

    /**
     * @var \OrgBundle\Entity\ActivityArea[]
     *
     * @Assert\All({
     *      @Assert\Type(type="OrgBundle\Entity\ActivityArea")
     * })
     */
    public $areas;

    /**
     * ActivityObject constructor.
     * @param $id
     * @param $name
     * @param $code
     * @param $canBeFilteredByCategory
     * @param $hasInterval
     * @param $isNegative
     * @param $indexes
     * @param $areas
     */
    public function __construct(
        $id,
        $name,
        $code,
        $canBeFilteredByCategory,
        $hasInterval,
        $isNegative,
        $indexes=[],
        $areas=[]
    )
    {
        $this->id = $id;
        $this->name = $name;
        $this->code = $code;
        $this->canBeFilteredByCategory = $canBeFilteredByCategory;
        $this->hasInterval = $hasInterval;
        $this->isNegative = $isNegative;
        $this->indexes = $indexes;
        $this->areas = $areas;
    }
}
