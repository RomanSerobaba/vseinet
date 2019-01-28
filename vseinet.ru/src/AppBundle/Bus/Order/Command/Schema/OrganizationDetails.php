<?php

namespace AppBundle\Bus\Order\Command\Schema;

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
     * @Assert\Length(min=20, max=20, exactMessage="Номер расчетного счёта должен состоять из 9 цифр")
     */
    public $settlementAccount;

    /**
     * @Assert\Type(type="string")
     * @Assert\Length(min=12, max=12, exactMessage="ИНН должен состоять из 12 цифр")
     */
    public $tin;

    /**
     * @Assert\Type(type="string")
     * @Assert\Length(min=9, max=9, exactMessage="КПП должен состоять из 9 цифр")
     */
    public $kpp;

    /**
     * @Assert\Type(type="string")
     * @Assert\Length(min=9, max=9, exactMessage="БИК должен состоять из 9 цифр")
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
        $this->bankId = empty($bankId) ? Null : (int) $bankId;
    }

    public function setWithVat($withVat)
    {
        $this->withVat = Null === $withVat ? Null : (bool) $comuserId;
    }
}
