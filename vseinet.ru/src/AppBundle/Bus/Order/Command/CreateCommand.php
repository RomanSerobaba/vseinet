<?php

namespace AppBundle\Bus\Order\Command;

use AppBundle\Bus\Message\Message;
use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Validator\Constraints as VIC;
use AppBundle\Validator\Constraints\Enum;
use AppBundle\Enum\OrderType;
use AppBundle\Enum\DeliveryTypeCode;
use AppBundle\Enum\PaymentTypeCode;
use AppBundle\Bus\Order\Command\Schema\Passport;
use AppBundle\Bus\Order\Command\Schema\Client;
use AppBundle\Bus\Order\Command\Schema\Address;
use AppBundle\Bus\Order\Command\Schema\OrganizationDetails;

class CreateCommand extends Message
{
    /**
     * @Assert\NotBlank(message="Укажите тип заказа")
     * @VIC\Enum("AppBundle\Enum\OrderType")
     */
    public $typeCode = OrderType::NATURAL;

    /**
     * @Assert\Type(type="AppBundle\Bus\Order\Command\Schema\Client")
     * @Assert\Valid
     */
    public $client;

    /**
     * @Assert\Type(type="AppBundle\Bus\Order\Command\Schema\Address")
     * @Assert\Valid
     */
    public $address;

    /**
     * @Assert\Type(type="AppBundle\Bus\Order\Command\Schema\Passport")
     * @Assert\Valid
     */
    public $passport;

    /**
     * @Assert\Type(type="AppBundle\Bus\Order\Command\Schema\OrganizationDetails")
     * @Assert\Valid
     */
    public $organizationDetails;

    /**
     * @Assert\Type(type="string")
     */
    public $geoCityName;

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
    public $creditDownPayment = 0;

    /**
     * @Enum("AppBundle\Enum\DeliveryTypeCode", message="Некорректное значение типа доставки")
     */
    public $deliveryTypeCode;

    /**
     * @Assert\Type(type="boolean", message="Признак того, нужен ли подъём клиенту")
     */
    public $needLifting;

    /**
     * @Assert\Type(type="integer", message="Идентификатор транспортной компании должен быть числом")
     */
    public $transportCompanyId;

    /**
     * @Assert\Type(type="boolean")
     */
    public $isNotificationNeeded;

    /**
     * @Assert\Type(type="boolean")
     */
    public $isMarketingSubscribed;

    /**
     * @Assert\Type(type="boolean")
     */
    public $isCallNeeded;

    /**
     * @Assert\Type(type="string")
     */
    public $callNeedComment;

    /**
     * @Assert\Type(type="string")
     */
    public $comment;

    /**
     * @Assert\Type(type="integer")
     */
    public $id;

    private function setSchema(&$schema, $data)
    {
        foreach ($data as $property => $value) {
            if (property_exists($schema, $property)) {
                $propertySetter = 'set'.ucfirst($property);
                if (method_exists($schema, $propertySetter)) {
                    $schema->$propertySetter($value);
                } else {
                    $schema->$property = $value;
                }
            }
        }
    }

    public function setAddress($address)
    {
        if (!empty($address)) {
            $addressSchema = new Address();
            $this->setSchema($addressSchema, $address);
            $this->address = $addressSchema;
        } else {
            $this->address = null;
        }
    }

    public function setClient($client)
    {
        if (!empty($client)) {
            $clientSchema = new Client();
            $this->setSchema($clientSchema, $client);
            $this->client = $clientSchema;
        } else {
            $this->client = null;
        }
    }

    public function setPassport($passport)
    {
        if (!$passport instanceof Passport) {
            if (!empty($passport)) {
                $passportSchema = new Passport();
                $this->setSchema($passportSchema, $passport);
                $this->passport = $passportSchema;
            } else {
                $this->passport = null;
            }
        } else {
            $this->passport = $passport;
        }
    }

    public function setOrganizationDetails($organizationDetails)
    {
        if (!empty($organizationDetails)) {
            $organizationDetailsSchema = new OrganizationDetails();
            $this->setSchema($organizationDetailsSchema, $organizationDetails);
            $this->organizationDetails = $organizationDetailsSchema;
        } else {
            $this->organizationDetails = null;
        }
    }

    public function setIsNotificationNeeded($isNotificationNeeded)
    {
        $this->isNotificationNeeded = null !== $isNotificationNeeded ? (bool) $isNotificationNeeded : $isNotificationNeeded;
    }

    public function setIsMarketingSubscribed($isMarketingSubscribed)
    {
        $this->isMarketingSubscribed = null !== $isMarketingSubscribed ? (bool) $isMarketingSubscribed : $isMarketingSubscribed;
    }

    public function setNeedLifting($needLifting)
    {
        $this->needLifting = null !== $needLifting ? (bool) $needLifting : $needLifting;
    }

    public function setHasLift($hasLift)
    {
        $this->hasLift = null !== $hasLift ? (bool) $hasLift : $hasLift;
    }

    public function setIsCallNeeded($isCallNeeded)
    {
        $this->isCallNeeded = null !== $isCallNeeded ? (bool) $isCallNeeded : $isCallNeeded;
    }

    public function setCreditDownPayment($creditDownPayment)
    {
        $this->creditDownPayment = null !== $creditDownPayment ? (int) $creditDownPayment : $creditDownPayment;
    }

    public function setPaymentTypeCode($paymentTypeCode)
    {
        if ($paymentTypeCode instanceof \AppBundle\Bus\Order\Query\DTO\PaymentType) {
            $this->paymentTypeCode = $paymentTypeCode->code;
        } else {
            $this->paymentTypeCode = $paymentTypeCode;
        }
    }

    public function setGeoCityId($geoCityId)
    {
        $this->geoCityId = null !== $geoCityId ? (int) $geoCityId : $geoCityId;
    }

    public function setGeoPointId($geoPointId)
    {
        if ($geoPointId instanceof \AppBundle\Bus\Order\Query\DTO\GeoPoint) {
            $this->geoPointId = $geoPointId->id;
        } else {
            $this->geoPointId = null !== $geoPointId ? (int) $geoPointId : $geoPointId;
        }
    }

    public function setTransportCompanyId($transportCompanyId)
    {
        if ($transportCompanyId instanceof \AppBundle\Bus\Order\Query\DTO\TransportCompany) {
            $this->transportCompanyId = $transportCompanyId->id;
        } else {
            $this->transportCompanyId = null !== $transportCompanyId ? (int) $transportCompanyId : $transportCompanyId;
        }
    }
}
