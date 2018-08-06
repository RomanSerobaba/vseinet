<?php 

namespace OrgBundle\Bus\Department\Query\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class Employee
{
    /**
     * Идентификатор сотрудника
     * @Assert\Type(type="integer")
     */
    public $id;

    /**
     * Ф.И.О. сотрудника
     * @Assert\Type(type="string")
     */
    public $name;

    /**
     * @Assert\Type(type="integer")
     */
    public $sortOrder;

    /**
     * @Assert\Type(type="string")
     */
    public $position;

    /**
     * @Assert\Type(type="boolean")
     */
    public $isActive;

    /**
     * @Assert\Type(type="DateTime")
     */
    public $startWorkingAt;

    /**
     * @Assert\Type(type="boolean")
     */
    public $isIrregular;

    /**
     * @Assert\Type(type="DateTime")
     */
    public $workSince;

    /**
     * @Assert\Type(type="DateTime")
     */
    public $workTill;

    /**
     * Employee constructor.
     * @param $id
     * @param $name
     * @param $sortOrder
     * @param $position
     * @param bool $isActive
     * @param $startWorkingAt
     * @param bool $isIrregular
     * @param $workSince
     * @param $workTill
     */
    public function __construct(
        $id,
        $name,
        $sortOrder,
        $position,
        $isActive,
        $startWorkingAt=null,
        $isIrregular=false,
        $workSince=null,
        $workTill=null
    )
    {
        $this->id = $id;
        $this->name = $name;
        $this->sortOrder = $sortOrder;
        $this->position = $position;
        $this->isActive = $isActive;
        $this->startWorkingAt = $startWorkingAt;
        $this->isIrregular = $isIrregular;
        $this->workSince = $workSince;
        $this->workTill = $workTill;
    }
}
