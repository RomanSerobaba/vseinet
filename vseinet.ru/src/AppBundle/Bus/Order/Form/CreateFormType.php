<?php

namespace AppBundle\Bus\Order\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Doctrine\ORM\EntityManagerInterface;
use AppBundle\Bus\User\Form\UserDataType;
use AppBundle\Bus\User\Form\IsHumanType;
use AppBundle\Bus\Order\Form\OrganizationDetailsType;
use AppBundle\Bus\Geo\Form\GeoAddressType;
use AppBundle\Bus\User\Form\PassportDataType;
use AppBundle\Bus\Order\Command\CreateCommand;
use AppBundle\Enum\OrderType;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use AppBundle\Enum\PaymentTypeCode;
use AppBundle\Enum\DeliveryTypeCode;
use AppBundle\Service\GeoCityIdentity;
use AppBundle\Entity\GeoCity;
use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Entity\GeoAddressToPerson;

class CreateFormType extends AbstractType
{
    /**
     * @var EntityManagerInterface
     */
    protected $em;

    /**
     * @var TokenStorageInterface
     */
    protected $security;

    /**
     * @var GeoCityIdentity
     */
    protected $geoCityIdentity;


    public function __construct(EntityManagerInterface $em, TokenStorageInterface $security, GeoCityIdentity $geoCityIdentity)
    {
        $this->em = $em;
        $this->security = $security;
        $this->geoCityIdentity = $geoCityIdentity;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $user = $this->security->getToken()->getUser();
        $builder
            ->add('typeCode', ChoiceType::class, ['choices' => array_flip(OrderType::getChoices(is_object($user) && $user->isEmployee())),])
            ->add('isHuman', IsHumanType::class)
            ->add('submit', SubmitType::class);

        switch ($options['data']->typeCode) {
            case OrderType::CONSUMABLES:
            case OrderType::EQUIPMENT:
            case OrderType::RESUPPLY:
                $this->addPointDataFields($builder, $options);
                break;

            case OrderType::LEGAL:
                $this->addUserDataFields($builder, $options);
                $this->addDeliveryTypesFields($builder, $options);
                $this->addPaymentTypesFields($builder, $options);
                $this->addAdditionalDataFields($builder, $options);
                $this->addOrganizationDetailsFields($builder, $options);
                break;

            case OrderType::NATURAL:
                $this->addUserDataFields($builder, $options);
                $this->addDeliveryTypesFields($builder, $options);
                $this->addPaymentTypesFields($builder, $options);
                $this->addAdditionalDataFields($builder, $options);
                break;

            case OrderType::RETAIL:
                $this->addUserDataFields($builder, $options);
                $this->addDeliveryTypesFields($builder, $options);
                $this->addPaymentTypesFields($builder, $options);
                break;
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => CreateCommand::class,
            'constraints' => [
                new Assert\Callback(function($data, $context){
                    if (in_array($data->typeCode, [OrderType::LEGAL, OrderType::NATURAL]) || OrderType::RETAIL == $data->deliveryTypeCode && (DeliveryTypeCode::COURIER == $data->deliveryTypeCode || in_array($data->paymentTypeCode, [PaymentTypeCode::CREDIT, PaymentTypeCode::INSTALLMENT]))) {
                        if (empty($data->userData->fullname)) {
                            $context->buildViolation('Необходимо указать ваше имя')
                                ->atPath('userData.fullname')
                                ->addViolation();
                        }

                    }

                    if (DeliveryTypeCode::TRANSPORT_COMPANY == $data->deliveryTypeCode) {
                        if (empty($data->passportData->seria)) {
                            $context->buildViolation('Для выбранного способа доставки необходимо заполнить паспортные данные')
                                ->atPath('passportData.seria')
                                ->addViolation();
                        }

                        if (empty($data->passportData->number)) {
                            $context->buildViolation('Для выбранного способа доставки необходимо заполнить паспортные данные')
                                ->atPath('passportData.number')
                                ->addViolation();
                        }

                        if (empty($data->passportData->issuedAt)) {
                            $context->buildViolation('Для выбранного способа доставки необходимо заполнить паспортные данные')
                                ->atPath('passportData.issuedAt')
                                ->addViolation();
                        }
                    } elseif (DeliveryTypeCode::POST == $data->deliveryTypeCode) {
                        if (empty($data->geoAddress->postalCode)) {
                            $context->buildViolation('Для выбранного способа доставки необходимо указать адрес')
                                ->atPath('geoAddress.postalCode')
                                ->addViolation();
                        }

                        if (empty($data->geoAddress->geoStreetId)) {
                            $context->buildViolation('Для выбранного способа доставки необходимо указать адрес')
                                ->atPath('geoAddress.geoStreetId')
                                ->addViolation();
                            $context->buildViolation('Для выбранного способа доставки необходимо указать адрес')
                                ->atPath('geoAddress.geoStreetName')
                                ->addViolation();
                        }

                        if (empty($data->geoAddress->house)) {
                            $context->buildViolation('Для выбранного способа доставки необходимо указать адрес')
                                ->atPath('geoAddress.house')
                                ->addViolation();
                        }

                        if (empty($data->geoAddress->building)) {
                            $context->buildViolation('Для выбранного способа доставки необходимо указать адрес')
                                ->atPath('geoAddress.building')
                                ->addViolation();
                        }

                        if (empty($data->geoAddress->apartment)) {
                            $context->buildViolation('Для выбранного способа доставки необходимо указать адрес')
                                ->atPath('geoAddress.apartment')
                                ->addViolation();
                        }
                    } elseif (DeliveryTypeCode::COURIER == $data->deliveryTypeCode) {
                        if (empty($data->geoAddress->geoStreetId)) {
                            $context->buildViolation('Для выбранного способа доставки необходимо указать адрес')
                                ->atPath('geoAddress.geoStreetId')
                                ->addViolation();
                            $context->buildViolation('Для выбранного способа доставки необходимо указать адрес')
                                ->atPath('geoAddress.geoStreetName')
                                ->addViolation();
                        }

                        if (empty($data->geoAddress->house) && empty($data->geoAddress->building)) {
                            $context->buildViolation('Для выбранного способа доставки необходимо указать адрес')
                                ->atPath('geoAddress.house')
                                ->addViolation();
                            $context->buildViolation('Для выбранного способа доставки необходимо указать адрес')
                                ->atPath('geoAddress.building')
                                ->addViolation();
                        }

                        if (empty($data->geoAddress->apartment)) {
                            $context->buildViolation('Для выбранного способа доставки необходимо указать адрес')
                                ->atPath('geoAddress.apartment')
                                ->addViolation();
                        }

                        if (($data->needLifting ?? FALSE) && empty($data->geoAddress->floor)) {
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
                })],
        ]);
    }

    private function addPointDataFields(FormBuilderInterface $builder, array &$options) {
        $user = $this->security->getToken()->getUser();

        if (count($user->geoRooms) > 0) {
            $points = array_column($user->geoRooms, 'geo_point_id');
        } else {
            $points = [$this->getParameter('default.point.id')];
        }

        $q = $this->em->createQuery("
            SELECT
                NEW AppBundle\Bus\Order\Query\DTO\GeoPoint (
                    p.id,
                    p.name,
                    a.address,
                    r.hasRetail,
                    r.hasDelivery,
                    r.hasRising,
                    p.geoCityId
                )
            FROM AppBundle:GeoPoint AS p
            JOIN AppBundle:Representative AS r WITH r.geoPointId = p.id
            LEFT JOIN AppBundle:GeoAddress AS a WITH a.id = p.geoAddressId
            WHERE p.id IN (:ids) AND r.isActive = TRUE
        ");
        $q->setParameter('ids', $points);
        $points = $q->getResult('IndexByHydrator');
        $point = reset($points);

        if (!empty($options['data']->geoPointId) && isset($points[$options['data']->geoPointId])) {
            $point = $points[$options['data']->geoPointId];
        }

        $builder
            ->add('geoPointId', ChoiceType::class, [
                'choices' => $points,
                'choice_label' => 'name',
                'choice_value' => 'id',
                'data' => $point,
            ]);
    }

    private function addUserDataFields(FormBuilderInterface $builder, array &$options) {
        $builder
            ->add('userData', UserDataType::class);
    }

    private function addPaymentTypesFields(FormBuilderInterface $builder, array &$options) {
        $user = $this->security->getToken()->getUser();
        $q = $this->em->createQuery("
            SELECT
                NEW AppBundle\Bus\Order\Query\DTO\PaymentType (
                    p.code,
                    p.name,
                    p.isInternal,
                    p.isRemote,
                    p.description
                )
            FROM AppBundle:PaymentType AS p
            WHERE p.isActive = TRUE
            ORDER BY p.code
        ");
        $paymentTypes = $q->getResult('IndexByHydrator');

        if (!is_object($user) || !$user->isEmployee()) {
            $paymentTypes = array_filter($paymentTypes, function($val){
                return !$val->isInternal;
            });
        }

        if (in_array($options['data']->deliveryTypeCode, [DeliveryTypeCode::TRANSPORT_COMPANY, DeliveryTypeCode::POST,])) {
            $paymentTypes = array_filter($paymentTypes, function($val){
                return $val->isRemote;
            });
        }

        if (OrderType::LEGAL == $options['data']->typeCode) {
            $paymentTypes = array_filter($paymentTypes, function($val){
                return in_array($val->code, [PaymentTypeCode::CASHLESS, PaymentTypeCode::CASH]);
            });
            $paymentTypeParams['data'] = PaymentTypeCode::CASHLESS;
        } else {
            $paymentTypeParams['data'] = PaymentTypeCode::CASH;
        }

        // if (false !== array_search(PaymentTypeCode::CREDIT, $paymentTypes)) {
            $builder
                ->add('creditDownPayment', TextType::class, ['required' => false,]);
        // }

        $paymentType = reset($paymentTypes);

        if (!empty($options['data']->paymentTypeCode) && isset($paymentTypes[$options['data']->paymentTypeCode])) {
            $paymentType = $paymentTypes[$options['data']->paymentTypeCode];
        }

        $paymentTypeParams['data'] = $options['data']->paymentTypeCode ?? $paymentTypeParams['data'];
        $options['data']->paymentTypeCode = $paymentType->code;

        $builder
            ->add('paymentTypeCode', ChoiceType::class, [
                'choices' => $paymentTypes,
                'choice_label' => 'name',
                'choice_value' => 'code',
                'data' => $paymentType,
            ]);
    }

    private function addDeliveryTypesFields(FormBuilderInterface $builder, array &$options) {
        $user = $this->security->getToken()->getUser();
        $allDeliveryTypes = DeliveryTypeCode::getChoices();
        $deliveryTypes = [];
        $deliveryType = DeliveryTypeCode::POST;
        $hasDelivery = false;

        if (OrderType::RETAIL != $options['data']->typeCode) {
            if (empty($options['data']->geoCityId)) {
                $geoCityId = $this->geoCityIdentity->getGeoCity()->getId();
            } else {
                $geoCityId = $options['data']->geoCityId;
            }

            $q = $this->em->createQuery("
                SELECT
                    NEW AppBundle\Bus\Order\Query\DTO\GeoPoint (
                        p.id,
                        p.name,
                        a.address,
                        r.hasRetail,
                        r.hasDelivery,
                        r.hasRising,
                        p.geoCityId
                    )
                FROM AppBundle:GeoPoint AS p
                JOIN AppBundle:Representative AS r WITH r.geoPointId = p.id
                LEFT JOIN AppBundle:GeoAddress AS a WITH a.id = p.geoAddressId
                WHERE p.geoCityId = :cityId AND r.isActive = TRUE AND (r.hasRetail = TRUE OR r.hasDelivery = TRUE)
            ");
            $q->setParameter('cityId', $geoCityId);
            $points = $q->getResult('IndexByHydrator');
        } else {
            $q = $this->em->createQuery("
                SELECT
                    NEW AppBundle\Bus\Order\Query\DTO\GeoPoint (
                        p.id,
                        p.name,
                        a.address,
                        r.hasRetail,
                        r.hasDelivery,
                        r.hasRising,
                        p.geoCityId
                    )
                FROM AppBundle:GeoPoint AS p
                JOIN AppBundle:Representative AS r WITH r.geoPointId = p.id
                LEFT JOIN AppBundle:GeoAddress AS a WITH a.id = p.geoAddressId
                WHERE p.id = :id
            ");
            $q->setParameter('id', $user->defaultGeoPointId);
            $points = $q->getResult('IndexByHydrator');
            $point = reset($points);
            $options['data']->geoCityId = $point->geoCityId;
            $geoCityId = $options['data']->geoCityId;
        }

        if (count($points) > 0) {
            array_walk($points, function($value) use (&$hasDelivery) {
                if ($value->hasDelivery) {
                    $hasDelivery = true;
                }
            });
            $points = array_filter($points, function($val){
                return $val->hasRetail;
            });

            if (count($points) > 0) {
                $deliveryTypes[array_search(DeliveryTypeCode::EX_WORKS, $allDeliveryTypes)] = DeliveryTypeCode::EX_WORKS;
                $deliveryType = DeliveryTypeCode::EX_WORKS;
                $point = reset($points);

                if (!empty($options['data']->geoPointId) && isset($points[$options['data']->geoPointId])) {
                    $point = $points[$options['data']->geoPointId];
                }

                $builder
                    ->add('geoPointId', ChoiceType::class, [
                        'choices' => $points,
                        'choice_label' => 'name',
                        'choice_value' => 'id',
                        'data' => $point,
                    ]);
            }

            if ($hasDelivery) {
                $deliveryTypes[array_search(DeliveryTypeCode::COURIER, $allDeliveryTypes)] = DeliveryTypeCode::COURIER;
                $addressDTO = NULL;

                if (is_object($user) && !$user->isEmployee()) {
                    $q = $this->em->createQuery("
                        SELECT
                            NEW AppBundle\Bus\Geo\Query\DTO\Address (
                                ga.geoStreetId,
                                gs.name,
                                ga.house,
                                ga.building,
                                ga.apartment,
                                ga.floor,
                                ga.hasLift,
                                ga.office,
                                ga.postalCode,
                                gs.geoCityId
                            )
                        FROM AppBundle:GeoAddressToPerson AS gap
                        JOIN AppBundle:GeoAddress AS ga WITH ga.id = gap.geoAddressId
                        LEFT JOIN AppBundle:GeoStreet AS gs WITH gs.id = ga.geoStreetId
                        WHERE gap.personId = :personId AND gap.isMain = TRUE
                    ");
                    $q->setParameter('personId', $user->getPersonId());
                    $addressDTO = $q->getSingleResult();

                    if ($geoCityId != $addressDTO->geoCityId) {
                        $addressDTO = NULL;
                    }
                }

                $builder
                    ->add('geoAddress', GeoAddressType::class, ['data' => $addressDTO])
                    ->add('needLifting', CheckBoxType::class, ['required' => false,]);
            }
        } else {
            $q = $this->em->createQuery("
                SELECT
                    NEW AppBundle\Bus\Order\Query\DTO\TransportCompany (
                        tc.id,
                        tc.name,
                        tc.tax,
                        COALESCE(tc.calculatorUrl, tc.url)
                    )
                FROM AppBundle:TransportCompany AS tc
                JOIN AppBundle:TransportCompanyTerminal AS tct WITH tct.transportCompanyId = tc.id
                WHERE tct.geoCityId = :cityId AND tc.isActive = TRUE AND tct.isActive = TRUE
                GROUP BY tc.id
                ORDER BY tc.sortOrder
            ");
            $q->setParameter('cityId', $geoCityId);
            $transportCompanies = $q->getResult('IndexByHydrator');

            if (count($transportCompanies) > 0) {
                $deliveryTypes[array_search(DeliveryTypeCode::TRANSPORT_COMPANY, $allDeliveryTypes)] = DeliveryTypeCode::TRANSPORT_COMPANY;
                $deliveryType = DeliveryTypeCode::TRANSPORT_COMPANY;
                $transportCompany = reset($transportCompanies);

                if (!empty($options['data']->transportCompanyId) && isset($transportCompanies[$options['data']->transportCompanyId])) {
                    $transportCompany = $transportCompanies[$options['data']->transportCompanyId];
                }
                $builder
                    ->add('transportCompanyId', ChoiceType::class, [
                        'choices' => $transportCompanies,
                        'choice_label' => 'name',
                        'choice_value' => 'id',
                        'data' => $transportCompany,
                    ])
                    ->add('passportData', PassportDataType::class);
            }

            $deliveryTypes[array_search(DeliveryTypeCode::POST, $allDeliveryTypes)] = DeliveryTypeCode::POST;
                $builder
                    ->add('geoAddress', GeoAddressType::class);
        }

        if (!empty($options['data']->deliveryTypeCode) && false !== array_search($options['data']->deliveryTypeCode, $deliveryTypes)) {
            $deliveryType = $options['data']->deliveryTypeCode;
        } else {
            $options['data']->deliveryTypeCode = $deliveryType;
        }

        $options['data']->geoCityId = $geoCityId;
        $city = $this->em->getRepository(GeoCity::class)->find($geoCityId);
        $builder
            ->add('geoCityId', HiddenType::class, [
                    'data' => $geoCityId,
                ])
            ->add('geoCityName', TextType::class, [
                    'data' => $city->getName(),
                ])
            ->add('deliveryTypeCode', ChoiceType::class, [
                'choices' => $deliveryTypes,
                'data' => $deliveryType,
            ]);
    }

    private function addAdditionalDataFields(FormBuilderInterface $builder, array &$options) {
        $user = $this->security->getToken()->getUser();
        $isCallNeeded = null;

        if (!empty($options['data']->isCallNeeded) && null !== $options['data']->isCallNeeded) {
            $isCallNeeded = $options['data']->isCallNeeded;
        }

        if (is_object($user) && $user->isEmployee()) {
            $isCallNeeded = false;
        }

        if (!empty($options['data']->isNotificationNeeded) && null !== $options['data']->isNotificationNeeded) {
            $isNotificationNeeded = $options['data']->isNotificationNeeded;
        } else {
            $isNotificationNeeded = TRUE;
        }

        $builder
            ->add('isCallNeeded', ChoiceType::class, [
                'choices' => ['Не требуется, со сроками доставки ознакомлен' => false, 'Требуется (у меня остались вопросы)' => true,],
                'data' => $isCallNeeded,
            ])
            ->add('callNeedComment', TextType::class, ['required' => false,])
            ->add('comment', TextareaType::class, ['required' => false,])
            ->add('isNotificationNeeded', CheckBoxType::class, ['required' => false, 'data' => $isNotificationNeeded,]);
    }

    private function addOrganizationDetailsFields(FormBuilderInterface $builder, array &$options) {
        $builder
            ->add('organizationDetails', OrganizationDetailsType::class);
    }
}
