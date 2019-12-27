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
use AppBundle\Bus\User\Form\IsHumanType;
use AppBundle\Bus\Order\Command\CreateCommand;
use AppBundle\Enum\OrderType;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use AppBundle\Enum\PaymentTypeCode;
use AppBundle\Enum\DeliveryTypeCode;
use AppBundle\Enum\ContactTypeCode;
use AppBundle\Service\GeoCityIdentity;
use AppBundle\Entity\GeoCity;
use Symfony\Component\DependencyInjection\ContainerInterface;
use AppBundle\Enum\UserRole;
use AppBundle\Enum\RepresentativeTypeCode;

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

    /**
     * @var ContainerInterface
     */
    protected $container;

    public function __construct(EntityManagerInterface $em, TokenStorageInterface $security, GeoCityIdentity $geoCityIdentity, ContainerInterface $container)
    {
        $this->em = $em;
        $this->security = $security;
        $this->geoCityIdentity = $geoCityIdentity;
        $this->container = $container;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $user = $this->security->getToken()->getUser();
        $isUserEmployee = is_object($user) && $user->isEmployee();

        $types = array_flip(OrderType::getChoices($isUserEmployee));

        if ($isUserEmployee) {
            $points = $this->getEmployeePoints();

            if (empty($points)) {
                $types = array_filter($types, function ($val) {
                    return !in_array($val, [OrderType::CONSUMABLES, OrderType::EQUIPMENT, OrderType::RESUPPLY]);
                });
            }
        }

        switch ($options['data']->typeCode) {
            case OrderType::CONSUMABLES:
            case OrderType::EQUIPMENT:
            case OrderType::RESUPPLY:
                $this->addPointDataFields($builder, $options, $points);
                break;

            case OrderType::LEGAL:
                $this->addClientDataFields($builder, $options);
                $this->addDeliveryTypesFields($builder, $options);
                $this->addPaymentTypesFields($builder, $options);
                $this->addAdditionalDataFields($builder, $options);
                $this->addOrganizationDetailsFields($builder, $options);
                break;

            case OrderType::NATURAL:
                $this->addClientDataFields($builder, $options);
                $this->addDeliveryTypesFields($builder, $options);
                $this->addPaymentTypesFields($builder, $options);
                $this->addAdditionalDataFields($builder, $options);
                break;

            case OrderType::RETAIL:
                $this->addClientDataFields($builder, $options);
                $this->addDeliveryTypesFields($builder, $options);
                $this->addPaymentTypesFields($builder, $options);
                break;
        }

        $builder
            ->add('typeCode', ChoiceType::class, ['required' => false, 'choices' => $types])
            ->add('isHuman', IsHumanType::class)
            ->add('submit', SubmitType::class);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => CreateCommand::class,
            'allow_extra_fields' => true,
        ]);
    }

    private function getEmployeePoints()
    {
        $user = $this->security->getToken()->getUser();

        if (count($user->geoRooms) > 0) {
            $points = array_column($user->geoRooms, 'geo_point_id');
        }

        if (!$user->isRoleIn([UserRole::PURCHASER, UserRole::ADMIN,UserRole::MANAGER]) && empty($points)) {
            return [];
        }

        $clause = '';
        $parameters = [];

        if (!$user->isRoleIn([UserRole::PURCHASER, UserRole::ADMIN, UserRole::MANAGER])) {
            $clause .= ' AND p.id IN (:ids)';
            $parameters['ids'] = $points;
        } else {
            $clause .= ' AND r.hasWarehouse = TRUE AND r.type IN (:representativeTypeCode_OUR, :representativeTypeCode_PARTNER, :representativeTypeCode_TORG)';
            $parameters['representativeTypeCode_OUR'] = RepresentativeTypeCode::OUR;
            $parameters['representativeTypeCode_PARTNER'] = RepresentativeTypeCode::PARTNER;
            $parameters['representativeTypeCode_TORG'] = RepresentativeTypeCode::TORG;
        }

        $q = $this->em->createQuery("
            SELECT
                NEW AppBundle\Bus\Order\Query\DTO\GeoPoint (
                    p.id,
                    CONCAT(CASE WHEN c.name = p.name THEN '' ELSE CONCAT(c.name, ', ') END, p.name),
                    a.address,
                    r.hasRetail,
                    r.hasDelivery,
                    r.hasRising,
                    p.geoCityId
                )
            FROM AppBundle:GeoPoint AS p
            JOIN AppBundle:Representative AS r WITH r.geoPointId = p.id
            LEFT JOIN AppBundle:GeoAddress AS a WITH a.id = p.geoAddressId
            JOIN AppBundle:GeoCity AS c WITH c.id = p.geoCityId
            WHERE r.isActive = TRUE{$clause}
            ORDER BY c.name, p.name
        ");
        $q->setParameters($parameters);

        return $q->getResult('IndexByHydrator');
    }

    private function addPointDataFields(FormBuilderInterface $builder, array &$options, $points)
    {
        $point = reset($points);

        if (!empty($options['data']->geoPointId) && !empty($points[$options['data']->geoPointId])) {
            $point = $points[$options['data']->geoPointId];
        }

        $options['data']->geoPointId = $point->id;
        $builder
            ->add('geoPointId', ChoiceType::class, [
                'required' => false,
                'choices' => $points,
                'choice_label' => 'name',
                'choice_value' => 'id',
                'data' => $point,
            ]);
    }

    private function addClientDataFields(FormBuilderInterface $builder, array &$options)
    {
        $user = $this->security->getToken()->getUser();
        $clientDTO = $options['data']->client;

        if (!empty($options['data']->isNotificationNeeded) && null !== $options['data']->isNotificationNeeded) {
            $isNotificationNeeded = $options['data']->isNotificationNeeded;
        } else {
            $isNotificationNeeded = true;
        }

        if (is_object($user) && !$user->isEmployee() && empty($clientDTO)) {
            $clientDTO = new \AppBundle\Bus\Order\Command\Schema\Client();
            $clientDTO->userId = $user->getId();
            $clientDTO->fullname = $user->person->getFullname();
            $q = $this->em->createQuery('
                SELECT
                    c,
                    CASE WHEN c.isMain = true THEN 1 ELSE 2 END AS HIDDEN ORD1,
                    CASE WHEN c.contactTypeCode = :mobile THEN 1 ELSE 2 END AS HIDDEN ORD2
                FROM AppBundle:Contact AS c
                WHERE c.personId = :personId AND c.contactTypeCode IN (:mobile, :phone)
                ORDER BY ORD1 ASC, ORD2 ASC
            ');
            $q->setParameter('personId', $user->getPersonId());
            $q->setParameter('mobile', ContactTypeCode::MOBILE);
            $q->setParameter('phone', ContactTypeCode::PHONE);
            $phoneList = $q->getResult();

            if (!empty($phoneList[0])) {
                if (ContactTypeCode::MOBILE == $phoneList[0]->getContactTypeCode()) {
                    $clientDTO->phone = $phoneList[0]->getValue();

                    if (!empty($phoneList[1])) {
                        $clientDTO->additionalPhone = $phoneList[1]->getValue();
                    }
                } else {
                    $clientDTO->additionalPhone = $phoneList[0]->getValue();
                }
            }

            $q = $this->em->createQuery('
                SELECT
                    c,
                    CASE WHEN c.isMain = true THEN 1 ELSE 2 END AS HIDDEN ORD
                FROM AppBundle:Contact AS c
                WHERE c.personId = :personId AND c.contactTypeCode IN (:email)
                ORDER BY ORD ASC
            ');
            $q->setParameter('personId', $user->getPersonId());
            $q->setParameter('email', ContactTypeCode::EMAIL);
            $email = $q->getOneOrNullResult();

            if (!empty($email)) {
                $clientDTO->email = $email->getValue();
            }

            $options['data']->client = $clientDTO;
        }

        if (null === $options['data']->isMarketingSubscribed && (!is_object($user) || $user->getIsMarketingSubscribed())) {
            $isMarketingSubscribed = true;
        } else {
            $isMarketingSubscribed = $options['data']->isMarketingSubscribed;
        }

        $options['data']->isMarketingSubscribed = $isMarketingSubscribed;
        $options['data']->isNotificationNeeded = $isNotificationNeeded;
        $builder
            ->add('isMarketingSubscribed', CheckboxType::class, [
                    'data' => $isMarketingSubscribed,
                    'required' => false,
                ])
            ->add('client', ClientType::class)
            ->add('isNotificationNeeded', CheckBoxType::class, ['required' => false, 'data' => $isNotificationNeeded]);
    }

    private function addPaymentTypesFields(FormBuilderInterface $builder, array &$options)
    {
        $user = $this->security->getToken()->getUser();
        $q = $this->em->createQuery("
            SELECT
                NEW AppBundle\Bus\Order\Query\DTO\PaymentType (
                    p.code,
                    p.name,
                    p.isInternal,
                    p.isRemote,
                    p.description,
                    p.cashlessPercent
                )
            FROM AppBundle:PaymentType AS p
            INNER JOIN AppBundle:RepresentativeToPaymentType AS r2pt WITH r2pt.paymentTypeCode = p.code
            WHERE p.isActive = TRUE AND r2pt.representativeId = :pointId
            ORDER BY p.code
        ")->setParameters(['pointId' => $options['data']->geoPointId,]);
        $paymentTypes = $q->getResult('IndexByHydrator');

        if (!is_object($user) || !$user->isEmployee()) {
            $paymentTypes = array_filter($paymentTypes, function ($val) {
                return !$val->isInternal;
            });
        }

        if (in_array($options['data']->deliveryTypeCode, [DeliveryTypeCode::TRANSPORT_COMPANY, DeliveryTypeCode::POST])) {
            $paymentTypes = array_filter($paymentTypes, function ($val) {
                return $val->isRemote;
            });
        }

        if (OrderType::LEGAL == $options['data']->typeCode) {
            $paymentTypes = array_filter($paymentTypes, function ($val) {
                return in_array($val->code, [PaymentTypeCode::CASHLESS, PaymentTypeCode::CASH]);
            });
            $paymentTypeParams['data'] = PaymentTypeCode::CASHLESS;
        } else {
            $paymentTypeParams['data'] = PaymentTypeCode::CASH;
        }

        // if (false !== array_search(PaymentTypeCode::CREDIT, $paymentTypes)) {
        $builder
                ->add('creditDownPayment', TextType::class, ['required' => false]);
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

    private function addAddressDataFields(FormBuilderInterface $builder, array &$options)
    {
        $user = $this->security->getToken()->getUser();
        $addressDTO = $options['data']->address;

        if (is_object($user) && !$user->isEmployee() && empty($addressDTO)) {
            $q = $this->em->createQuery("
                SELECT
                    NEW AppBundle\Bus\Order\Command\Schema\Address (
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
            $addressDTO = $q->getOneOrNullResult();

            if (!empty($addressDTO) && $options['data']->geoCityId != $addressDTO->geoCityId) {
                $addressDTO = null;
            }

            $options['data']->address = $addressDTO;
        }

        $builder
            ->add('address', AddressType::class, ['data' => $addressDTO]);
    }

    private function addDeliveryTypesFields(FormBuilderInterface $builder, array &$options)
    {
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
            $geoCityId = $point->geoCityId;
        }

        if (count($points) > 0) {
            array_walk($points, function ($value) use (&$hasDelivery) {
                if ($value->hasDelivery) {
                    $hasDelivery = true;
                }
            });
            $points = array_filter($points, function ($val) {
                return $val->hasRetail;
            });

            if (count($points) > 0) {
                $deliveryTypes[array_search(DeliveryTypeCode::EX_WORKS, $allDeliveryTypes)] = DeliveryTypeCode::EX_WORKS;
                $deliveryType = DeliveryTypeCode::EX_WORKS;
                $point = reset($points);

                if (!empty($options['data']->geoPointId) && isset($points[$options['data']->geoPointId])) {
                    $point = $points[$options['data']->geoPointId];
                }

                $options['data']->geoPointId = $point->id;
                $builder
                    ->add('geoPointId', ChoiceType::class, [
                        'required' => false,
                        'choices' => $points,
                        'choice_label' => 'name',
                        'choice_value' => 'id',
                        'data' => $point,
                    ]);
            }

            if ($hasDelivery) {
                $deliveryTypes[array_search(DeliveryTypeCode::COURIER, $allDeliveryTypes)] = DeliveryTypeCode::COURIER;
                $this->addAddressDataFields($builder, $options);
                $builder
                    ->add('needLifting', CheckBoxType::class, [
                        'required' => false,
                        'attr' => [
                            'canBeLifted' => $point->hasRising,
                        ],
                    ]);
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

                if (!empty($options['data']->transportCompanyId)) {
                    $transportCompany = $transportCompanies[$options['data']->transportCompanyId];
                }

                $options['data']->transportCompanyId = $transportCompany->id;
                $builder
                    ->add('transportCompanyId', ChoiceType::class, [
                        'required' => false,
                        'choices' => $transportCompanies,
                        'choice_label' => 'name',
                        'choice_value' => 'id',
                        'data' => $transportCompany,
                    ])
                    ->add('passport', PassportType::class);
            }

            $deliveryTypes[array_search(DeliveryTypeCode::POST, $allDeliveryTypes)] = DeliveryTypeCode::POST;
            $this->addAddressDataFields($builder, $options);
        }

        if (!empty($options['data']->deliveryTypeCode) && false !== array_search($options['data']->deliveryTypeCode, $deliveryTypes)) {
            $deliveryType = $options['data']->deliveryTypeCode;
        }

        $city = $this->em->getRepository(GeoCity::class)->find($geoCityId);

        $options['data']->deliveryTypeCode = $deliveryType;
        $options['data']->geoCityId = $geoCityId;
        $options['data']->geoCityName = $city->getName();
        $builder
            ->add('geoCityId', HiddenType::class, [
                    'data' => $geoCityId,
                ])
            ->add('geoCityName', TextType::class, [
                    'required' => false,
                    'data' => $city->getName(),
                ])
            ->add('deliveryTypeCode', ChoiceType::class, [
                    'required' => false,
                    'choices' => $deliveryTypes,
                    'data' => $deliveryType,
                ]);
    }

    private function addAdditionalDataFields(FormBuilderInterface $builder, array &$options)
    {
        $user = $this->security->getToken()->getUser();
        $isCallNeeded = null;

        if (isset($options['data']->isCallNeeded) && null !== $options['data']->isCallNeeded) {
            $isCallNeeded = $options['data']->isCallNeeded;
        }

        if (is_object($user) && $user->isEmployee()) {
            $isCallNeeded = false;
        }

        $options['data']->isCallNeeded = $isCallNeeded;
        $builder
            ->add('isCallNeeded', ChoiceType::class, [
                'choices' => ['Не требуется, со сроками доставки ознакомлен' => false, 'Требуется (у меня остались вопросы)' => true],
                'data' => $isCallNeeded,
                'required' => false,
            ])
            ->add('callNeedComment', TextType::class, ['required' => false])
            ->add('comment', TextareaType::class, ['required' => false]);
    }

    private function addOrganizationDetailsFields(FormBuilderInterface $builder, array &$options)
    {
        $builder
            ->add('organizationDetails', OrganizationDetailsType::class)
            ->add('withVat', ChoiceType::class, [
                'required' => false,
                'choices' => ['Приобрести товар без НДС' => false, 'Приобрести товар с НДС' => true],
            ]);
    }
}
