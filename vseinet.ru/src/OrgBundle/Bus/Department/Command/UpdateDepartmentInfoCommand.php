<?php

namespace OrgBundle\Bus\Department\Command;

use AppBundle\Annotation as VIA;
use AppBundle\Bus\Message\Message;
use Symfony\Component\Validator\Constraints as Assert;

class UpdateDepartmentInfoCommand extends Message
{
    /**
     * @VIA\Description("Department id")
     * @Assert\Type(type="integer")
     * @Assert\GreaterThan(0)
     * @Assert\NotBlank(
     *     message="Department id should not be blank."
     * )
     */
    public $id;

    /**
     * @Assert\Type(type="string")
     * @Assert\NotBlank()
     */
    public $name;

    /**
     * @Assert\Type(type="string")
     * @Assert\NotBlank()
     */
    public $typeCode;

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
}