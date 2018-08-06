<?php 

namespace OrgBundle\Bus\Work\Query\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class Employee
{
    /**
     * Идентификатор сотрудника
     * @Assert\Type(type="integer")
     */
    public $employeeId;

    /**
     * Идентификатор подразделения
     * @Assert\Type(type="integer")
     */
    public $departmentId;

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
     * @Assert\Type(type="integer")
     */
    public $amount;

    /**
     * @Assert\Type(type="integer")
     */
    public $tax;

    /**
     * @Assert\Type(type="integer")
     */
    public $paid;

    /**
     * @Assert\Type(type="integer")
     */
    public $fines;

    /**
     * @Assert\Type(type="float")
     */
    public $coefficient;

    /**
     * @Assert\Type(type="integer")
     */
    public $queueAmount;


    /**
     * Employee constructor.
     * @param $employeeId
     * @param $departmentId
     * @param $name
     * @param $sortOrder
     * @param $position
     * @param $amount
     * @param $tax
     * @param $paid
     * @param $fines
     * @param $coefficient
     * @param $queueAmount
     */
    public function __construct(
        $employeeId,
        $departmentId,
        $name,
        $sortOrder,
        $position,
        $amount,
        $tax,
        $paid,
        $fines,
        $coefficient,
        $queueAmount
    )
    {
        $this->employeeId = $employeeId;
        $this->departmentId = $departmentId;
        $this->name = $name;
        $this->sortOrder = $sortOrder;
        $this->position = $position;
        $this->amount = $amount;
        $this->tax = $tax;
        $this->paid = $paid;
        $this->fines = $fines;
        $this->coefficient = $coefficient;
        $this->queueAmount = $queueAmount;
    }
}
