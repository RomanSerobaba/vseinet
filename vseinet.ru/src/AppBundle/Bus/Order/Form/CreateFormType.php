<?php

namespace AppBundle\Bus\Order\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\{ TextType, ChoiceType, SubmitType, TextareaType };
use Doctrine\ORM\EntityManagerInterface;
use AppBundle\Bus\User\Form\{ UserDataType, IsHumanType };
use AppBundle\Bus\Order\Command\CreateCommand;
use AppBundle\Enum\OrderType;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use AppBundle\Enum\PaymentTypeCode;

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


    public function __construct(EntityManagerInterface $em, TokenStorageInterface $security)
    {
        $this->em = $em;
        $this->security = $security;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        // $stmt = $this->em->getConnection()->query("
        //     SELECT c.value
        //     FROM contact AS c
        //     INNER JOIN org_contact AS oc ON oc.contact_id = c.id
        //     INNER JOIN org_employee AS oe ON oe.user_id = oc.org_employee_user_id
        //     WHERE EXISTS (
        //         SELECT 1
        //         FROM org_employment_history
        //         WHERE org_employee_user_id = oe.user_id AND fired_at IS NULL
        //     )
        //     GROUP BY c.value
        //     ORDER BY c.value
        // ");
        // $phones = $stmt->fetchAll(\PDO::FETCH_COLUMN);
        // $choicesPhones = array_combine($phones, $phones);
        $user = $this->security->getToken()->getUser();
        $builder
            ->add('typeCode', ChoiceType::class, ['choices' => array_flip(OrderType::getChoices(is_object($user) && $user->isEmployee())),])
            ->add('isHuman', IsHumanType::class)
            ->add('submit', SubmitType::class);

        $stmt = $this->em->getConnection()->prepare("
            SELECT code, name
            FROM payment_type
            WHERE is_active = TRUE".(!is_object($user) || !$user->isEmployee() ? " AND is_internal = FALSE" : "")."
        ");
        $stmt->execute();
        $paymentTypes = $stmt->fetchAll(\PDO::FETCH_ASSOC);

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
                $pointsIds = array_column($points, 'id');
                $pointsNames = array_column($points, 'name');

                $builder
                    ->add('geoPointId', ChoiceType::class, [
                        'choices' => array_combine($pointsNames, $pointsIds),
                        'data' => reset($pointsIds),
                        ]);
                break;

            case OrderType::LEGAL:
                $builder
                    ->add('userData', UserDataType::class)
                    ->add('paymentTypeCode', ChoiceType::class, [
                        'choices' => array_combine(array_column($paymentTypes, 'name'), array_column($paymentTypes, 'code')),
                        'data' => PaymentTypeCode::CASHLESS,
                        ]);
                break;

            case OrderType::NATURAL:
                $builder
                    ->add('userData', UserDataType::class)
                    ->add('paymentTypeCode', ChoiceType::class, [
                        'choices' => array_combine(array_column($paymentTypes, 'name'), array_column($paymentTypes, 'code')),
                        'data' => PaymentTypeCode::CASH,
                        ]);
                break;

            case OrderType::RETAIL:
                $builder
                    ->add('userData', UserDataType::class)
                    ->add('paymentTypeCode', ChoiceType::class, [
                        'choices' => array_combine(array_column($paymentTypes, 'name'), array_column($paymentTypes, 'code')),
                        'data' => PaymentTypeCode::CASH,
                        ]);
                break;
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => CreateCommand::class,
        ]);
    }
}
