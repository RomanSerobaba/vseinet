<?php

namespace AppBundle\Bus\Cart\Query;

use AppBundle\Bus\Message\Message;
use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Validator\Constraints\Enum;
use AppBundle\Enum\DeliveryTypeCode;
use AppBundle\Enum\PaymentTypeCode;

class GetQuery extends Message
{
    /**
     * @Assert\Type(type="string")
     */
    public $discountCode;

    /**
     * @Assert\Type(type="integer")
     */
    public $geoPointId;

    /**
     * @Enum("AppBundle\Enum\PaymentTypeCode")
     */
    public $paymentTypeCode = PaymentTypeCode::CASH;

    /**
     * @Enum("AppBundle\Enum\DeliveryTypeCode")
     */
    public $deliveryTypeCode = DeliveryTypeCode::EX_WORKS;

    /**
     * @Assert\Type(type="boolean")
     */
    public $needLifting = false;

    /**
     * @Assert\Type(type="boolean")
     */
    public $hasLift = false;

    /**
     * @Assert\Type(type="integer")
     */
    public $floor = 1;

    /**
     * @Assert\Type(type="integer")
     */
    public $transportCompanyId;

}
