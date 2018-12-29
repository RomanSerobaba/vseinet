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
     * @Assert\Type(type="integer")
     */
    public $id;
}
