<?php

namespace AdminBundle\Bus\Competitor\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
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
        $competitors = $this->em->getRepository(Competitor::class)->getEditable();
        $competitorsChoice = [];
        foreach ($competitors as $competitor) {
            $competitorsChoice[$competitor->getName()] = $competitor->getId();
        }

        $builder
            ->add('id', HiddenType::class, ['required' => false])
            ->add('competitorId', ChoiceType::class, ['choices' => $competitorsChoice])
            ->add('baseProductId', HiddenType::class)
            ->add('url', TextType::class, ['required' => false])
            ->add('price', PriceType::class, ['required' => false])
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
