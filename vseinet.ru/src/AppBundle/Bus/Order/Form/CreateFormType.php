<?php

namespace AppBundle\Bus\Order\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\{ TextType, ChoiceType, SubmitType, TextareaType };
use Doctrine\ORM\EntityManagerInterface;
use AppBundle\Bus\User\Form\{ UserDataType, IsHumanType };
use AppBundle\Bus\Order\Form\OrganizationDetailsType;
use AppBundle\Bus\Order\Command\CreateCommand;
use AppBundle\Enum\OrderType;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use AppBundle\Enum\PaymentTypeCode;
use AppBundle\Enum\DeliveryTypeCode;
use AppBundle\Service\GeoCityIdentity;

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
                if (count($user->geoRooms) > 0) {
                    $points = array_column($user->geoRooms, 'geo_point_id');
                } else {
                    $points = [$this->getParameter('default.point.id')];
                }

                $stmt = $this->em->getConnection()->prepare("
                    SELECT p.id, CONCAT_WS(', ', c.name, p.name) AS name
                    FROM geo_point AS p
                    INNER JOIN representative AS r ON r.geo_point_id = p.id
                    INNER JOIN geo_city AS c ON c.id = p.geo_city_id
                    WHERE p.id IN (:point_ids)
                ");
                $stmt->execute(['point_ids' => implode(',', $points)]);
                $points = $stmt->fetchAll(\PDO::FETCH_ASSOC);
                $points = array_combine(array_column($points, 'name'), array_column($points, 'id'));

                $builder
                    ->add('geoPointId', ChoiceType::class, [
                        'choices' => $points,
                        'data' => $options['data']->geoPointId ?? reset($points),
                        ]);
                break;

            case OrderType::LEGAL:
                $this->addUserDataFields($builder);
                $this->addDeliveryTypesFields($builder, $options);
                $this->addPaymentTypesFields($builder, $options);
                $this->addAdditionalDataFields($builder);
                $this->addOrganizationDetailsFields($builder);
                break;

            case OrderType::NATURAL:
                $this->addUserDataFields($builder);
                $this->addDeliveryTypesFields($builder, $options);
                $this->addPaymentTypesFields($builder, $options);
                $this->addAdditionalDataFields($builder);
                break;

            case OrderType::RETAIL:
                $this->addUserDataFields($builder);
                $this->addDeliveryTypesFields($builder, $options);
                $this->addPaymentTypesFields($builder, $options);
                break;
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => CreateCommand::class,
        ]);
    }

    private function addUserDataFields(FormBuilderInterface $builder) {
        $builder
            ->add('userData', UserDataType::class);
    }

    private function addPaymentTypesFields(FormBuilderInterface $builder, array $options) {
        $user = $this->security->getToken()->getUser();
        $paymentTypeParams = [];
        $stmt = $this->em->getConnection()->prepare("
            SELECT code, name, is_internal, is_remote
            FROM payment_type
            WHERE is_active = TRUE
        ");
        $stmt->execute();
        $paymentTypes = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        if (!is_object($user) || !$user->isEmployee()) {
            $paymentTypes = array_filter($paymentTypes, function($val){
                return !$val['is_internal'];
            });
        }

        if (DeliveryTypeCode::TRANSPORT_COMPANY == $options['data']->deliveryTypeCode) {
            $paymentTypes = array_filter($paymentTypes, function($val){
                return $val['is_remote'];
            });
        }

        if (OrderType::LEGAL == $options['data']->typeCode) {
            $paymentTypes = array_filter($paymentTypes, function($val){
                return in_array($val['code'], [PaymentTypeCode::CASHLESS, PaymentTypeCode::CASH]);
            });
            $paymentTypeParams['data'] = PaymentTypeCode::CASHLESS;
        } else {
            $paymentTypeParams['data'] = PaymentTypeCode::CASH;
        }

        $paymentTypes = array_combine(array_column($paymentTypes, 'name'), array_column($paymentTypes, 'code'));

        if (false !== array_search(PaymentTypeCode::CREDIT, $paymentTypes)) {
            $builder
                ->add('creditInitialContribution', TextType::class, ['required' => false,]);
        }

        $paymentTypeParams['data'] = $options['data']->paymentTypeCode ?? $paymentTypeParams['data'];
        $paymentTypeParams['choices'] = $paymentTypes;

        $builder
            ->add('paymentTypeCode', ChoiceType::class, $paymentTypeParams);
    }

    private function addDeliveryTypesFields(FormBuilderInterface $builder, array $options) {
        $user = $this->security->getToken()->getUser();
        $geoCityId = $this->geoCityIdentity->getGeoCity()->getId();
        $allDeliveryTypes = DeliveryTypeCode::getChoices();
        $deliveryTypes = [];
        $deliveryType = DeliveryTypeCode::POST;
        $hasDelivery = false;

        $q = $this->em->createQuery("
            SELECT
                NEW AppBundle\Bus\Order\Query\DTO\GeoPoint (
                    p.id,
                    p.name,
                    a.address,
                    r.hasRetail,
                    r.hasDelivery,
                    r.hasRising
                )
            FROM AppBundle:GeoPoint AS p
            JOIN AppBundle:Representative AS r WITH r.geoPointId = p.id
            LEFT JOIN AppBundle:GeoAddress AS a WITH a.id = p.geoAddressId
            WHERE p.geoCityId = :cityId AND r.isActive = TRUE AND (r.hasRetail = TRUE OR r.hasDelivery = TRUE)
        ");
        $q->setParameter('cityId', $geoCityId);
        $points = $q->getResult('IndexByHydrator');

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
                    ]);
            }

            $deliveryTypes[array_search(DeliveryTypeCode::POST, $allDeliveryTypes)] = DeliveryTypeCode::POST;
        }

        if (!empty($options['data']->deliveryTypeCode) && false !== array_search($options['data']->deliveryTypeCode, $deliveryTypes)) {
            $deliveryType = $options['data']->deliveryTypeCode;
        }

        $builder
            ->add('deliveryTypeCode', ChoiceType::class, [
                'choices' => $deliveryTypes,
                'data' => $deliveryType,
            ]);
    }

    private function addAdditionalDataFields(FormBuilderInterface $builder) {
        $user = $this->security->getToken()->getUser();
        $needCallParams = [
            'choices' => ['Не требуется, со сроками доставки ознакомлен' => false, 'Требуется (у меня остались вопросы)' => true,],
        ];

        if (is_object($user) && $user->isEmployee()) {
            $needCallParams['data'] = false;
        }

        $builder
            ->add('needCall', ChoiceType::class, $needCallParams)
            ->add('needCallComment', TextType::class, ['required' => false,])
            ->add('comment', TextareaType::class, ['required' => false,]);
    }

    private function addOrganizationDetailsFields(FormBuilderInterface $builder) {
        $builder
            ->add('organizationDetails', OrganizationDetailsType::class);
    }
}
