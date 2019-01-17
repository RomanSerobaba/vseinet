<?php

namespace AppBundle\Bus\Order\Command;

use AppBundle\Bus\Message\Message;
use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Validator\Constraints as VIC;
use AppBundle\Validator\Constraints\Enum;
use AppBundle\Enum\OrderType;
use AppBundle\Enum\DeliveryTypeCode;
use AppBundle\Enum\PaymentTypeCode;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

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
     * @Assert\Type(type="AppBundle\Bus\Geo\Query\DTO\Address")
     * @Assert\Valid
     */
    public $geoAddress;

    /**
     * @Assert\Type(type="AppBundle\Bus\User\Query\DTO\Passport")
     * @Assert\Valid
     */
    public $passportData;

    /**
     * @Assert\Type(type="AppBundle\Bus\Order\Query\DTO\OrganizationDetails")
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
     * @Enum("AppBundle\Enum\DeliveryTypeCode")
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
    public $isTranscationalSubscribed;

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

    private function setDTO(&$DTO, $data)
    {
        foreach ($data as $property => $value) {
            if (property_exists($DTO, $property)) {
                $propertySetter = 'set'.ucfirst($property);
                if (method_exists($DTO, $propertySetter)) {
                    $DTO->$propertySetter($value);
                } else {
                    $DTO->$property = $value;
                }
            }
        }
    }

    public function setGeoAddress($geoAddress)
    {
        if (!empty($geoAddress)) {
            $geoAddressDTO = new \AppBundle\Bus\Geo\Query\DTO\Address();
            $this->setDTO($geoAddressDTO, $geoAddress);
            $this->geoAddress = $geoAddressDTO;
        } else {
            $this->geoAddress = null;
        }
    }

    public function setUserData($userData)
    {
        if (!empty($userData)) {
            $userDataDTO = new \AppBundle\Bus\User\Query\DTO\UserData();
            $this->setDTO($userDataDTO, $userData);
            $this->userData = $userDataDTO;
        } else {
            $this->userData = null;
        }
    }

    public function setPassportData($passportData)
    {
        if (!empty($passportData)) {
            $passportDataDTO = new \AppBundle\Bus\User\Query\DTO\Passport();
            $this->setDTO($passportDataDTO, $passportData);
            $this->passportData = $passportDataDTO;
        } else {
            $this->passportData = null;
        }
    }

    public function setOrganizationDetails($organizationDetails)
    {
        if (!empty($organizationDetails)) {
            $organizationDetailsDTO = new \AppBundle\Bus\Order\Query\DTO\OrganizationDetails();
            $this->setDTO($organizationDetailsDTO, $organizationDetails);
            $this->organizationDetails = $organizationDetailsDTO;
        } else {
            $this->organizationDetails = null;
        }
    }

    public function setIsMarketingSubscribed($isMarketingSubscribed)
    {
        $this->isMarketingSubscribed = null !== $isMarketingSubscribed ? (bool) $isMarketingSubscribed : $isMarketingSubscribed;
    }

    public function setIsTranscationalSubscribed($isTranscationalSubscribed)
    {
        $this->isTranscationalSubscribed = null !== $isTranscationalSubscribed ? (bool) $isTranscationalSubscribed : $isTranscationalSubscribed;
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

    public function setFloor($floor)
    {
        $this->floor = null !== $floor ? (int) $floor : $floor;
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

    /**
     * @Assert\Callback
     */

    public function validate(ExecutionContextInterface $context, $payload)
    {
        if (OrderType::LEGAL == $this->typeCode) {
            if (empty($this->userData->position)) {
                $context->buildViolation('Необходимо указать вашу должность')
                    ->atPath('userData.position')
                    ->addViolation();
            }
        }

        if (in_array($this->typeCode, [OrderType::LEGAL, OrderType::NATURAL]) || OrderType::RETAIL == $this->deliveryTypeCode && (DeliveryTypeCode::COURIER == $this->deliveryTypeCode || in_array($this->paymentTypeCode, [PaymentTypeCode::CREDIT, PaymentTypeCode::INSTALLMENT]))) {
            if (empty($this->userData->fullname)) {
                $context->buildViolation('Необходимо указать ваше имя')
                    ->atPath('userData.fullname')
                    ->addViolation();
            }
        }

        if (DeliveryTypeCode::TRANSPORT_COMPANY == $this->deliveryTypeCode) {
            if (empty($this->passportData->seria) || empty($this->passportData->number) || empty($this->passportData->issuedAt)) {
                $context->buildViolation('Для выбранного способа доставки необходимо заполнить паспортные данные')
                    ->atPath('passportData.seria')
                    ->addViolation();
                $context->buildViolation('Для выбранного способа доставки необходимо заполнить паспортные данные')
                    ->atPath('passportData.number')
                    ->addViolation();
                $context->buildViolation('Для выбранного способа доставки необходимо заполнить паспортные данные')
                    ->atPath('passportData.issuedAt')
                    ->addViolation();
            }
        } elseif (DeliveryTypeCode::POST == $this->deliveryTypeCode) {
            if (empty($this->geoAddress->postalCode) || empty($this->geoAddress->geoStreetName) || empty($this->geoAddress->house) && empty($this->geoAddress->building) || empty($this->geoAddress->apartment)) {
                $context->buildViolation('Для выбранного способа доставки необходимо указать адрес')
                    ->atPath('geoAddress.postalCode')
                    ->addViolation();
                $context->buildViolation('Для выбранного способа доставки необходимо указать адрес')
                    ->atPath('geoAddress.geoStreetId')
                    ->addViolation();
                $context->buildViolation('Для выбранного способа доставки необходимо указать адрес')
                    ->atPath('geoAddress.geoStreetName')
                    ->addViolation();
                $context->buildViolation('Для выбранного способа доставки необходимо указать адрес')
                    ->atPath('geoAddress.house')
                    ->addViolation();
                $context->buildViolation('Для выбранного способа доставки необходимо указать адрес')
                    ->atPath('geoAddress.building')
                    ->addViolation();
                $context->buildViolation('Для выбранного способа доставки необходимо указать адрес')
                    ->atPath('geoAddress.apartment')
                    ->addViolation();
            }

            if (empty($this->geoAddress->geoStreetId)) {
                $context->buildViolation('Выберите улицу из выпадающего списка, появляющегося при вводе')
                    ->atPath('geoAddress.postalCode')
                    ->addViolation();
                $context->buildViolation('Выберите улицу из выпадающего списка, появляющегося при вводе')
                    ->atPath('geoAddress.geoStreetId')
                    ->addViolation();
                $context->buildViolation('Выберите улицу из выпадающего списка, появляющегося при вводе')
                    ->atPath('geoAddress.geoStreetName')
                    ->addViolation();
                $context->buildViolation('Выберите улицу из выпадающего списка, появляющегося при вводе')
                    ->atPath('geoAddress.house')
                    ->addViolation();
                $context->buildViolation('Выберите улицу из выпадающего списка, появляющегося при вводе')
                    ->atPath('geoAddress.building')
                    ->addViolation();
                $context->buildViolation('Выберите улицу из выпадающего списка, появляющегося при вводе')
                    ->atPath('geoAddress.apartment')
                    ->addViolation();
            }
        } elseif (DeliveryTypeCode::COURIER == $this->deliveryTypeCode) {
            if (empty($this->geoAddress->geoStreetName) || empty($this->geoAddress->house) && empty($this->geoAddress->building) || empty($this->geoAddress->apartment)) {
                $context->buildViolation('Для выбранного способа доставки необходимо указать адрес')
                    ->atPath('geoAddress.geoStreetId')
                    ->addViolation();
                $context->buildViolation('Для выбранного способа доставки необходимо указать адрес')
                    ->atPath('geoAddress.geoStreetName')
                    ->addViolation();
                $context->buildViolation('Для выбранного способа доставки необходимо указать адрес')
                    ->atPath('geoAddress.house')
                    ->addViolation();
                $context->buildViolation('Для выбранного способа доставки необходимо указать адрес')
                    ->atPath('geoAddress.building')
                    ->addViolation();
                $context->buildViolation('Для выбранного способа доставки необходимо указать адрес')
                    ->atPath('geoAddress.apartment')
                    ->addViolation();
            }

            if (empty($this->geoAddress->geoStreetId)) {
                $context->buildViolation('Выберите улицу из выпадающего списка, появляющегося при вводе')
                    ->atPath('geoAddress.geoStreetId')
                    ->addViolation();
                $context->buildViolation('Выберите улицу из выпадающего списка, появляющегося при вводе')
                    ->atPath('geoAddress.geoStreetName')
                    ->addViolation();
                $context->buildViolation('Выберите улицу из выпадающего списка, появляющегося при вводе')
                    ->atPath('geoAddress.house')
                    ->addViolation();
                $context->buildViolation('Выберите улицу из выпадающего списка, появляющегося при вводе')
                    ->atPath('geoAddress.building')
                    ->addViolation();
                $context->buildViolation('Выберите улицу из выпадающего списка, появляющегося при вводе')
                    ->atPath('geoAddress.apartment')
                    ->addViolation();
            }

            if (($this->needLifting ?? FALSE) && empty($this->geoAddress->floor)) {
                $context->buildViolation('Вы указали, что вам нужен подъём, но не указали, на какой этаж')
                    ->atPath('needLifting')
                    ->addViolation();
                $context->buildViolation('Вы указали, что вам нужен подъём, но не указали, на какой этаж')
                    ->atPath('geoAddress.floor')
                    ->addViolation();
                $context->buildViolation('Вы указали, что вам нужен подъём, но не указали, на какой этаж')
                    ->atPath('geoAddress.hasLift')
                    ->addViolation();
            }

        }
    }
}
