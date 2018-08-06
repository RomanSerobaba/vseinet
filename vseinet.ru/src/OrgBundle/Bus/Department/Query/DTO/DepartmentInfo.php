<?php

namespace OrgBundle\Bus\Department\Query\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class DepartmentInfo
{
    /**
     * @Assert\Type(type="string")
     */
    public $number;

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
     * @Assert\Type(type="integer")
     */
    public $salaryDay;

    /**
     * @Assert\Choice({"cash_desk", "settlement_account", "bank_card", "e_wallet"})
     */
    public $salaryPaymentType;

    /**
     * @Assert\Type(type="integer")
     */
    public $salaryPaymentSource;

    /**
     * @Assert\Type(type="integer")
     */
    public $representativeId;

    /**
     * DepartmentInfo constructor.
     * @param $number
     * @param $name
     * @param $typeCode
     * @param $isActive
     * @param $salaryDay
     * @param $salaryPaymentType
     * @param $salaryPaymentSource
     * @param $representativeId
     */
    public function __construct(
        $number,
        $name,
        $typeCode,
        $isActive,
        $salaryDay,
        $salaryPaymentType,
        $salaryPaymentSource,
        $representativeId=null
    )
    {
        $this->number = $number;
        $this->name = $name;
        $this->typeCode = $typeCode;
        $this->isActive = $isActive;
        $this->salaryDay = $salaryDay;
        $this->salaryPaymentType = $salaryPaymentType;
        $this->salaryPaymentSource = $salaryPaymentSource;
        $this->representativeId = $representativeId;
    }
}