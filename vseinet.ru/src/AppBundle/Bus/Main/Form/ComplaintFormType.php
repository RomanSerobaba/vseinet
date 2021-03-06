<?php

namespace AppBundle\Bus\Main\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Doctrine\ORM\EntityManagerInterface;
use AppBundle\Bus\User\Form\UserDataType;
use AppBundle\Bus\User\Form\IsHumanType;
use AppBundle\Bus\Main\Command\ComplaintCommand;
use AppBundle\Enum\ComplaintType;

class ComplaintFormType extends AbstractType
{
    /**
     * @var EntityManagerInterface
     */
    protected $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $stmt = $this->em->getConnection()->query("
            SELECT p.firstname
            FROM person AS p
            INNER JOIN \"user\" AS u ON u.person_id = p.id
            INNER JOIN org_employee AS oe ON oe.user_id = u.id
            WHERE LENGTH(p.firstname) > 0 AND p.firstname NOT LIKE '%Test%' AND EXISTS (
                SELECT 1
                FROM org_employment_history
                WHERE org_employee_user_id = oe.user_id AND fired_at IS NULL
            )
            GROUP BY p.firstname
            ORDER BY p.firstname
        ");
        $managers = $stmt->fetchAll(\PDO::FETCH_COLUMN);
        $choicesManagers = array_combine($managers, $managers);

        $stmt = $this->em->getConnection()->query('
            SELECT c.value
            FROM contact AS c
            INNER JOIN org_contact AS oc ON oc.contact_id = c.id
            INNER JOIN org_employee AS oe ON oe.user_id = oc.org_employee_user_id
            WHERE EXISTS (
                SELECT 1
                FROM org_employment_history
                WHERE org_employee_user_id = oe.user_id AND fired_at IS NULL
            )
            GROUP BY c.value
            ORDER BY c.value
        ');
        $phones = $stmt->fetchAll(\PDO::FETCH_COLUMN);
        $choicesPhones = array_combine($phones, $phones);

        $builder
            ->add('type', ChoiceType::class, ['choices' => array_flip(ComplaintType::getChoices())])
            ->add('managerName', ChoiceType::class, ['choices' => $choicesManagers, 'required' => false, 'placeholder' => 'не помню'])
            ->add('managerPhone', ChoiceType::class, ['choices' => $choicesPhones, 'required' => false, 'placeholder' => 'не помню'])
            ->add('text', TextareaType::class)
            ->add('userData', UserDataType::class, ['additional_phone' => false])
            ->add('isHuman', IsHumanType::class)
            ->add('submit', SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ComplaintCommand::class,
        ]);
    }
}
