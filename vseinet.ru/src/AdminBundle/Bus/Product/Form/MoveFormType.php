<?php 

namespace AdminBundle\Bus\Product\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\{ HiddenType, SubmitType };
use AdminBundle\Bus\Category\Form\ChoiceFormType;
use AdminBundle\Bus\Category\Command\ChoiceCommand;
use AdminBundle\Bus\Product\Command\MoveCommand;

class MoveFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('id', HiddenType::class)
            ->add('category', ChoiceFormType::class)
            ->add('submit', SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => MoveCommand::class,
        ]);
    }
}
