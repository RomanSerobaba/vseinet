<?php

namespace AppBundle\Bus\User\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use AppBundle\Form\Type\HiddenIntType;
use AppBundle\Form\Type\PhoneType;
use AppBundle\Bus\User\Query\DTO\UserData;
use Symfony\Component\Validator\Constraints\NotBlank;

class UserDataType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $emailOptions = ['required' => $options['email_required']];
        if ($options['email_required']) {
            $emailOptions['constraints'] = [new NotBlank(['message' => 'Укажите Ваш email'])];
        }

        $builder
            ->add('fullname', TextType::class, ['required' => true])
            ->add('phone', PhoneType::class, ['required' => true])
            ->add('email', EmailType::class, $emailOptions)
            ->add('userId', HiddenIntType::class)
            ->add('comuserId', HiddenIntType::class)
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
