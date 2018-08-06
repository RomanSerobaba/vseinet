<?php

namespace OrgBundle\Bus\Work\Query\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class PaymentQueueItem
{
    /**
     * @Assert\Type(type="integer")
     */
    public $employeeUserId;

    /**
     * @Assert\Type(type="striing")
     */
    public $employeeName;

    /**
     * @Assert\Type(type="\DateTime")
     */
    public $date;

    /**
     * @Assert\Type(type="integer")
     */
    public $amount;

    /**
     * @Assert\Type(type="integer")
     */
    public $financialResourceId;

    /**
     * @Assert\Type(type="striing")
     */
    public $financialResourceName;

    /**
     * PaymentQueueItem constructor.
     * @param $employeeUserId
     * @param $employeeName
     * @param $date
     * @param $amount
     * @param $financialResourceId
     * @param $financialResourceName
     */
    public function __construct($employeeUserId, $employeeName, $date, $amount, $financialResourceId, $financialResourceName)
    {
        $this->employeeUserId = $employeeUserId;
        $this->employeeName = $employeeName;
        $this->date = $date;
        $this->amount = $amount;
        $this->financialResourceId = $financialResourceId;
        $this->financialResourceName = $financialResourceName;
    }
}