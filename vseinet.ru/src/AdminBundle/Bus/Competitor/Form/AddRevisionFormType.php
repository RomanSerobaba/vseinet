<?php 

namespace AdminBundle\Bus\Competitor\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\Extension\Core\Type\{ ChoiceType, HiddenType, SubmitType, TextType };
use AppBundle\Form\Type\PriceType;
use AppBundle\Entity\Competitor;
use AdminBundle\Bus\Competitor\Command\AddRevisionCommand;

class AddRevisionFormType extends AbstractType
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
        $competitors = $this->em->getRepository(Competitor::class)->getActive();
        $competitorsChoice = [];
        foreach ($competitors as $competitor) {
            $competitorsChoice[$competitor->getName()] = $competitor->getId();
        }

        $builder
            ->add('id', HiddenType::class, ['required' => false])
            ->add('competitorId', ChoiceType::class, ['choices' => $competitorsChoice])
            ->add('productId', HiddenType::class)
            ->add('link', TextType::class, ['required' => false])
            ->add('competitorPrice', PriceType::class, ['required' => false])
            ->add('submit', SubmitType::class)
        ;
    }
    
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => AddRevisionCommand::class,
        ]);
    }
}
