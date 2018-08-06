<?php 

namespace OrgBundle\Bus\Department\Query\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class Department
{
    /**
     * @Assert\Type(type="integer")
     */
    public $pid;

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
    public $typeCode;

    /**
     * @Assert\Type(type="boolean")
     */
    public $isActive;

    /**
     * @Assert\Type(type="string")
     */
    public $number;

    /**
     * @Assert\Type(type="integer")
     */
    public $sortOrder;

    /**
     * @Assert\Type(type="integer")
     */
    public $chiefId;

    /**
     * @Assert\Type(type="integer")
     */
    public $deputyId;

    /**
     * @Assert\Type(type="boolean")
     */
    public $isInterimChief;

    /**
     * @Assert\Type(type="array<integer>")
     */
    public $childrenIds;

    /**
     * @Assert\Type(type="array<integer>")
     */
    public $employeesIds;

    /**
     * Department constructor.
     * @param $pid
     * @param $id
     * @param $name
     * @param $typeCode
     * @param $isActive
     * @param $number
     * @param $sortOrder
     * @param $chiefId
     * @param $deputyId
     * @param $isInterimChief
     * @param $childrenIds
     * @param $employeesIds
     */
    public function __construct(
        $pid,
        $id,
        $name,
        $typeCode,
        $isActive,
        $number,
        $sortOrder,
        $chiefId=null,
        $deputyId=null,
        $isInterimChief=null,
        $childrenIds=null,
        $employeesIds=null)
    {
        $this->pid = $pid;
        $this->id = $id;
        $this->name = $name;
        $this->typeCode = $typeCode;
        $this->isActive = $isActive;
        $this->number = $number;
        $this->sortOrder = $sortOrder;
        $this->chiefId = $chiefId;
        $this->deputyId = $deputyId;
        $this->isInterimChief = $isInterimChief;
        $this->childrenIds = $childrenIds;
        $this->employeesIds = $employeesIds;
    }
}
