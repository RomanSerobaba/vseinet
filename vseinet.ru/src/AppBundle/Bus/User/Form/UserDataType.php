<?php

namespace AppBundle\Bus\User\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use AppBundle\Form\Type\PhoneType;
use AppBundle\Bus\User\Query\DTO\UserData;

class UserDataType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('fullname', TextType::class, ['required' => true])
            ->add('phone', PhoneType::class, ['required' => true])
            ->add('email', EmailType::class, ['required' => $options['email_required']])
            ->add('userId', HiddenType::class)
            ->add('comuserId', HiddenType::class)
        ;
        if ($options['additional_phone']) {
            $builder->add('additionalPhone', TextType::class, ['required' => false]);
        }
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'additional_phone' => true,
            'email_required' => false,
            'data_class' => UserData::class,
        ]);
    }
}
