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

        $builder
            ->add('typeCode', ChoiceType::class, ['choices' => array_flip(OrderType::getChoices($this->security->getToken()->getUser()->isEmployee())),])
            ->add('userData', UserDataType::class)
            // ->add('managerPhone', ChoiceType::class, ['choices' => $choicesPhones, 'required' => false, 'placeholder' => 'не помню'])
            // ->add('userData', UserDataType::class)
            ->add('isHuman', IsHumanType::class)
            ->add('submit', SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => CreateCommand::class,
        ]);
    }
}
