<?php

namespace AppBundle\Bus\User\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use AppBundle\Bus\User\Command\AddAddressCommand;

class AddAddressType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('geoCityName', TextType::class, ['required' => false])
            ->add('geoCityId', HiddenType::class)
            ->add('geoStreetName', TextType::class, ['required' => false])
            ->add('geoStreetId', HiddenType::class)
            ->add('house', TextType::class)
            ->add('building', TextType::class, ['required' => false])
            ->add('apartment', TextType::class, ['required' => false])
            ->add('floor', IntegerType::class, ['required' => false])
            ->add('hasLift', CheckboxType::class, ['required' => false])
            ->add('comment', TextType::class, ['required' => false])
            ->add('isMain', CheckboxType::class, ['required' => false])
            ->add('submit', SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => AddAddressCommand::class,
        ]);
    }
}
