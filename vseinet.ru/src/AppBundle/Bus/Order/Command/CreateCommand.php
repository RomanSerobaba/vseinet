<?php

namespace AppBundle\Bus\Order\Command;

use AppBundle\Bus\Message\Message;
use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Validator\Constraints as VIC;
use AppBundle\Validator\Constraints\Enum;
use AppBundle\Enum\OrderType;

class CreateCommand extends Message
{
    /**
     * @Assert\NotBlank(message="Укажите тип заказа")
     * @VIC\Enum("AppBundle\Enum\OrderType")
     */
    public $typeCode = OrderType::NATURAL;

    /**
     * @Assert\Type(type="AppBundle\Bus\User\Query\DTO\UserData")
     * @Assert\Valid
     */
    public $userData;

    /**
     * @Assert\Type(type="AppBundle\Bus\Order\Query\DTO\OrganizationDetails")
     * @Assert\Valid
     */
    public $organizationDetails;

    /**
     * @Assert\Type(type="integer", message="Идентификатор города должен быть числом")
     */
    public $geoCityId;

    /**
     * @Assert\Type(type="integer", message="Идентификатор розничной точки должен быть числом")
     */
    public $geoPointId;

    /**
     * @Enum("AppBundle\Enum\PaymentTypeCode")
     */
    public $paymentTypeCode;

    /**
     * @Assert\Type(type="integer", message="Сумма первоначального взноса должная быть целым числом")
     */
    public $creditInitialContribution = 0;

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
     * @Assert\Type(type="integer", message="Номер этажа должен быть числом")
     */
    public $floor;

    /**
     * @Assert\Type(type="integer", message="Идентификатор транспортной компании должен быть числом")
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
     * @Assert\Type(type="integer")
     */
    public $id;

    public function setCreditInitialContribution($creditInitialContribution) {
        $this->creditInitialContribution = null !== $creditInitialContribution ? (int) $creditInitialContribution : $creditInitialContribution;
    }

    public function setFloor($floor) {
        $this->floor = null !== $floor ? (int) $floor : $floor;
    }

    public function setPaymentTypeCode($paymentTypeCode) {
        if ($paymentTypeCode instanceof \AppBundle\Bus\Order\Query\DTO\PaymentType) {
            $this->paymentTypeCode = $paymentTypeCode->code;
        } else {
            $this->paymentTypeCode = $paymentTypeCode;
        }
    }

    public function setGeoPointId($geoPointId) {
        if ($geoPointId instanceof \AppBundle\Bus\Order\Query\DTO\GeoPoint) {
            $this->geoPointId = $geoPointId->id;
        } else {
            $this->geoPointId = null !== $geoPointId ? (int) $geoPointId : $geoPointId;
        }
    }

    public function setTransportCompanyId($transportCompanyId) {
        if ($transportCompanyId instanceof \AppBundle\Bus\Order\Query\DTO\TransportCompany) {
            $this->transportCompanyId = $transportCompanyId->id;
        } else {
            $this->transportCompanyId = null !== $transportCompanyId ? (int) $transportCompanyId : $transportCompanyId;
        }
    }
}
