<?php

namespace AppBundle\Bus\Order\Command\Schema;

use AppBundle\Bus\Message\Message;
use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Validator\Constraints as VIC;

class OrganizationDetails extends Message
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

    public function setBankId($bankId)
    {
        $this->bankId = empty($bankId) ? null : (int) $bankId;
    }

    public function setSettlementAccount($settlementAccount)
    {
        $this->bankId = empty($settlementAccount) ? null : $settlementAccount;
    }

    public function setTin($tin)
    {
        $this->tin = empty($tin) ? null : $tin;
    }

    public function setKpp($kpp)
    {
        $this->kpp = empty($kpp) ? null : $kpp;
    }

    public function setBic($bic)
    {
        $this->bic = empty($bic) ? null : $bic;
    }
}
