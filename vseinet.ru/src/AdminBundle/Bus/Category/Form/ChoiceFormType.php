<?php 

namespace AdminBundle\Bus\Category\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\{ HiddenType, TextType };
use AdminBundle\Bus\Category\Command\ChoiceCommand;


class ChoiceFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('q', TextType::class, ['required' => false])
            ->add('id', HiddenType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ChoiceCommand::class,
        ]);
    }
}
