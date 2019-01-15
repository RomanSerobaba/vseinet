<?php

namespace AppBundle\Bus\Order\Query\DTO;

use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Bus\User\Query\DTO\Contact;
use AppBundle\Enum\OrderItemStatus;

class OrganizationDetails
{
    /**
     * @Assert\Type(type="boolean")
     */
    public $withVat;

    /**
     * @Assert\Type(type="string")
     */
    public $name;

    /**
     * @Assert\Type(type="string")
     */
    public $legalAddress;

    /**
     * @Assert\Type(type="string")
     * @Assert\Length(min=20, max=20)
     */
    public $settlementAccount;

    /**
     * @Assert\Type(type="string")
     * @Assert\Length(min=12, max=12)
     */
    public $tin;

    /**
     * @Assert\Type(type="string")
     * @Assert\Length(min=9, max=9)
     */
    public $kpp;

    /**
     * @Assert\Type(type="string")
     * @Assert\Length(min=9, max=9)
     */
    public $bic;

    /**
     * @Assert\Type(type="integer", message="Идентификатор банка должен быть числом")
     */
    public $bankId;

    /**
     * @Assert\Type(type="string")
     */
    public $bankName;
}
