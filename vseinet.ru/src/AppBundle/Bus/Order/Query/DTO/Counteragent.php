<?php

namespace AppBundle\Bus\Order\Query\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class Counteragent
{
    /**
     * @Assert\Type(type="string")
     */
    public $kpp;

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
     */
    public $settlementAccount;

    /**
     * @Assert\Type(type="string")
     */
    public $bic;

    /**
     * @Assert\Type(type="string")
     */
    public $bankName;

    /**
     * @Assert\Type(type="integer")
     */
    public $bankId;

    public function __construct($kpp, $name, $legalAddress, $settlementAccount, $bic, $bankName, $bankId)
    {
        $this->kpp = $kpp;
        $this->name = $name;
        $this->legalAddress = $legalAddress;
        $this->settlementAccount = $settlementAccount;
        $this->bic = $bic;
        $this->bankName = $bankName;
        $this->bankId = $bankId;
    }
}
