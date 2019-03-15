<?php

namespace AppBundle\Bus\User\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use AppBundle\Form\Type\PhoneType;
use AppBundle\Bus\User\Query\DTO\UserData;

class UserDataType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('position', TextType::class, ['required' => false])
            ->add('fullname', TextType::class, ['required' => false])
            ->add('phone', PhoneType::class, ['required' => false])
            ->add('additionalPhone', TextType::class, ['required' => false])
            ->add('email', TextType::class, ['required' => false])
            ->add('userId', HiddenType::class)
            ->add('comuserId', HiddenType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => UserData::class,
        ]);
    }
}
