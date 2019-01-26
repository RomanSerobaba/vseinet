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
use AppBundle\Bus\Order\Query\DTO\Passport;

class CreateCommand extends Message
{
    /**
     * @Assert\NotBlank(message="Укажите тип заказа")
     * @VIC\Enum("AppBundle\Enum\OrderType")
     */
    public $typeCode = OrderType::NATURAL;

    /**
     * @Assert\Type(type="AppBundle\Bus\Order\Query\DTO\Client")
     * @Assert\Valid
     */
    public $client;

    /**
     * @Assert\Type(type="AppBundle\Bus\Order\Query\DTO\Address")
     * @Assert\Valid
     */
    public $address;

    /**
     * @Assert\Type(type="AppBundle\Bus\Order\Query\DTO\Passport")
     * @Assert\Valid
     */
    public $passport;

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

    public function setAddress($address)
    {
        if (!empty($address)) {
            $addressDTO = new \AppBundle\Bus\Order\Query\DTO\Address();
            $this->setDTO($addressDTO, $address);
            $this->address = $addressDTO;
        } else {
            $this->address = null;
        }
    }

    public function setClient($client)
    {
        if (!empty($client)) {
            $clientDTO = new \AppBundle\Bus\Order\Query\DTO\Client();

            if (!empty($client['userId'])) {
                $client['userId'] = (int) $client['userId'];
            } else {
                $client['userId'] = Null;
            }

            $this->setDTO($clientDTO, $client);
            $this->client = $clientDTO;
        } else {
            $this->client = null;
        }
    }

    public function setPassport($passport)
    {
        if (!$passport instanceof Passport) {
            if (!empty($passport)) {
                $passportDTO = new Passport();

                if (!empty($passport['issuedAt']) && preg_match('~^[0-3]\d{1}.[0-1]\d{1}.\d{4}$~isu', $passport['issuedAt'])) {
                    $passport['issuedAt'] = new \Datetime(date('Y-m-d', strtotime($passport['issuedAt'])));
                } elseif (empty($passport['issuedAt'])) {
                    $passport['issuedAt'] = NULL;
                }

                $this->setDTO($passportDTO, $passport);
                $this->passport = $passportDTO;
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
            $organizationDetailsDTO = new \AppBundle\Bus\Order\Query\DTO\OrganizationDetails();
            $this->setDTO($organizationDetailsDTO, $organizationDetails);
            $this->organizationDetails = $organizationDetailsDTO;
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

    /**
     * @Assert\Callback
     */

    public function validate(ExecutionContextInterface $context, $payload)
    {
        if (OrderType::LEGAL == $this->typeCode) {
            if (empty($this->organizationDetails->tin)) {
                $context->buildViolation('Необходимо указать ИНН вашей организации')
                    ->atPath('organizationDetails.tin')
                    ->addViolation();
            }
        }

        if (in_array($this->typeCode, [OrderType::LEGAL, OrderType::NATURAL]) || OrderType::RETAIL == $this->deliveryTypeCode && (DeliveryTypeCode::COURIER == $this->deliveryTypeCode || in_array($this->paymentTypeCode, [PaymentTypeCode::CREDIT, PaymentTypeCode::INSTALLMENT]))) {
            if (empty($this->client->fullname)) {
                $context->buildViolation('Необходимо указать ваше имя')
                    ->atPath('client.fullname')
                    ->addViolation();
            }
            if (empty($this->client->phone) && empty($this->client->additionalPhone)) {
                $context->buildViolation('Необходимо заполнить хотя бы один контактный номер (основной или дополнительный)')
                    ->atPath('client.phone')
                    ->addViolation();
            }
        }

        if (DeliveryTypeCode::TRANSPORT_COMPANY == $this->deliveryTypeCode) {
            if (OrderType::LEGAL != $this->typeCode && (empty($this->passport->seria) || empty($this->passport->number) || empty($this->passport->issuedAt))) {
                $context->buildViolation('Для выбранного способа доставки необходимо заполнить паспортные данные')
                    ->atPath('passport.seria')
                    ->addViolation();
                $context->buildViolation('Для выбранного способа доставки необходимо заполнить паспортные данные')
                    ->atPath('passport.number')
                    ->addViolation();
                $context->buildViolation('Для выбранного способа доставки необходимо заполнить паспортные данные')
                    ->atPath('passport.issuedAt')
                    ->addViolation();
            }
        } elseif (DeliveryTypeCode::POST == $this->deliveryTypeCode) {
            if (empty($this->address->postalCode) || empty($this->address->geoStreetName) || empty($this->address->house) && empty($this->address->building)) {
                $context->buildViolation('Для выбранного способа доставки необходимо указать адрес')
                    ->atPath('address.postalCode')
                    ->addViolation();
                $context->buildViolation('Для выбранного способа доставки необходимо указать адрес')
                    ->atPath('address.geoStreetId')
                    ->addViolation();
                $context->buildViolation('Для выбранного способа доставки необходимо указать адрес')
                    ->atPath('address.geoStreetName')
                    ->addViolation();
                $context->buildViolation('Для выбранного способа доставки необходимо указать адрес')
                    ->atPath('address.house')
                    ->addViolation();
                $context->buildViolation('Для выбранного способа доставки необходимо указать адрес')
                    ->atPath('address.building')
                    ->addViolation();
                $context->buildViolation('Для выбранного способа доставки необходимо указать адрес')
                    ->atPath('address.apartment')
                    ->addViolation();
            }

            if (empty($this->address->geoStreetId)) {
                $context->buildViolation('Выберите улицу из выпадающего списка, появляющегося при вводе')
                    ->atPath('address.postalCode')
                    ->addViolation();
                $context->buildViolation('Выберите улицу из выпадающего списка, появляющегося при вводе')
                    ->atPath('address.geoStreetId')
                    ->addViolation();
                $context->buildViolation('Выберите улицу из выпадающего списка, появляющегося при вводе')
                    ->atPath('address.geoStreetName')
                    ->addViolation();
                $context->buildViolation('Выберите улицу из выпадающего списка, появляющегося при вводе')
                    ->atPath('address.house')
                    ->addViolation();
                $context->buildViolation('Выберите улицу из выпадающего списка, появляющегося при вводе')
                    ->atPath('address.building')
                    ->addViolation();
                $context->buildViolation('Выберите улицу из выпадающего списка, появляющегося при вводе')
                    ->atPath('address.apartment')
                    ->addViolation();
            }
        } elseif (DeliveryTypeCode::COURIER == $this->deliveryTypeCode) {
            if (empty($this->address->geoStreetName) || empty($this->address->house) && empty($this->address->building)) {
                $context->buildViolation('Для выбранного способа доставки необходимо указать адрес')
                    ->atPath('address.geoStreetId')
                    ->addViolation();
                $context->buildViolation('Для выбранного способа доставки необходимо указать адрес')
                    ->atPath('address.geoStreetName')
                    ->addViolation();
                $context->buildViolation('Для выбранного способа доставки необходимо указать адрес')
                    ->atPath('address.house')
                    ->addViolation();
                $context->buildViolation('Для выбранного способа доставки необходимо указать адрес')
                    ->atPath('address.building')
                    ->addViolation();
                $context->buildViolation('Для выбранного способа доставки необходимо указать адрес')
                    ->atPath('address.apartment')
                    ->addViolation();
            }

            if (empty($this->address->geoStreetId)) {
                $context->buildViolation('Выберите улицу из выпадающего списка, появляющегося при вводе')
                    ->atPath('address.geoStreetId')
                    ->addViolation();
                $context->buildViolation('Выберите улицу из выпадающего списка, появляющегося при вводе')
                    ->atPath('address.geoStreetName')
                    ->addViolation();
                $context->buildViolation('Выберите улицу из выпадающего списка, появляющегося при вводе')
                    ->atPath('address.house')
                    ->addViolation();
                $context->buildViolation('Выберите улицу из выпадающего списка, появляющегося при вводе')
                    ->atPath('address.building')
                    ->addViolation();
                $context->buildViolation('Выберите улицу из выпадающего списка, появляющегося при вводе')
                    ->atPath('address.apartment')
                    ->addViolation();
            }

            if (($this->needLifting ?? FALSE) && empty($this->address->floor)) {
                $context->buildViolation('Вы указали, что вам нужен подъём, но не указали, на какой этаж')
                    ->atPath('needLifting')
                    ->addViolation();
                $context->buildViolation('Вы указали, что вам нужен подъём, но не указали, на какой этаж')
                    ->atPath('address.floor')
                    ->addViolation();
                $context->buildViolation('Вы указали, что вам нужен подъём, но не указали, на какой этаж')
                    ->atPath('address.hasLift')
                    ->addViolation();
            }

        }
    }
}
