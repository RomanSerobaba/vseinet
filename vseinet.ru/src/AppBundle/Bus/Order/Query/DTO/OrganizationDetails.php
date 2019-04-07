<?php

namespace AppBundle\Bus\Order\Query\DTO;

use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Validator\Constraints as VIC;

class OrganizationDetails
{
    /**
     * @Assert\Type(type="string")
     */
    public $name;

    /**
     * @Assert\Type(type="string")
     */
    public $legalAddress;

    /**
     * @VIC\SettlementAccount
     */
    public $settlementAccount;

    /**
     * @VIC\TIN
     */
    public $tin;

    /**
     * @VIC\KPP
     */
    public $kpp;

    /**
     * @VIC\BIC
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
