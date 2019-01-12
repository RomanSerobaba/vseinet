<?php

namespace AppBundle\Bus\Cart\Query;

use AppBundle\Bus\Message\Message;
use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Validator\Constraints\Enum;
use AppBundle\Enum\DeliveryTypeCode;
use AppBundle\Enum\PaymentTypeCode;
use AppBundle\Enum\OrderType;

class GetSummaryQuery extends Message
{
    /**
     * @Assert\Type(type="AppBundle\Bus\Cart\Query\DTO\Cart")
     */
    public $cart;

    /**
     * @Enum("AppBundle\Enum\OrderType")
     */
    public $orderTypeCode;

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
    public $needLifting = false;

    /**
     * @Assert\Type(type="boolean")
     */
    public $hasLift = false;

    /**
     * @Assert\Type(type="integer", message="Этаж должен быть числом")
     */
    public $floor = 1;

    /**
     * @Assert\Type(type="integer", message="Идентификатор транспортной компании должен быть числом")
     */
    public $transportCompanyId;

    /**
     * @Assert\Type(type="integer", message="Идентификатор розничной точки должен быть числом")
     */
    public $geoPointId;

}
