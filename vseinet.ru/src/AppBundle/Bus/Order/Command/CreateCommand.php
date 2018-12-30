<?php

namespace AppBundle\Bus\Order\Command;

use AppBundle\Bus\Message\Message;
use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Validator\Constraints as VIC;
use AppBundle\Validator\Constraints\Enum;

class CreateCommand extends Message
{
    /**
     * @Assert\NotBlank(message="Укажите тип заказа")
     * @VIC\Enum("AppBundle\Enum\OrderType")
     */
    public $typeCode;

    /**
     * @Assert\Type(type="AppBundle\Bus\User\Query\DTO\UserData")
     * @Assert\Valid
     */
    public $userData;

    /**
     * @Assert\Type(type="integer")
     */
    public $geoPointId;

    /**
     * @Enum("AppBundle\Enum\PaymentTypeCode")
     */
    public $paymentTypeCode;

    /**
     * @Enum("AppBundle\Enum\DeliveryTypeCode")
     */
    public $deliveryTypeCode;

    /**
     * @Assert\Type(type="boolean")
     */
    public $needLifting;

    /**
     * @Assert\Type(type="boolean")
     */
    public $hasLift;

    /**
     * @Assert\Type(type="integer")
     */
    public $floor;

    /**
     * @Assert\Type(type="integer")
     */
    public $transportCompanyId;

    /**
     * @Assert\Type(type="boolean")
     */
    public $needCall;

    /**
     * @Assert\Type(type="string")
     */
    public $needCallComment;

    /**
     * @Assert\Type(type="string")
     */
    public $comment;

    /**
     * @Assert\Type(type="boolean")
     */
    public $withVat;

    /**
     * @Assert\Type(type="string")
     */
    public $counteragentName;

    /**
     * @Assert\Type(type="string")
     */
    public $counteragentLegalAddress;

    /**
     * @Assert\Type(type="string")
     * @Assert\Length(min=20, max=20)
     */
    public $counteragentSettlementAccount;

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
     * @Assert\Type(type="integer")
     */
    public $id;
}
