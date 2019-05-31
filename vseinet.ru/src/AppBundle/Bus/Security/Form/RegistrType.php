<?php

namespace AppBundle\Bus\Security\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use AppBundle\Enum\PersonGender;

class RegistrType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('lastname', TextType::class)
            ->add('firstname', TextType::class)
            ->add('secondname', TextType::class, ['required' => false])
            ->add('gender', ChoiceType::class, ['expanded' => true, 'choices' => array_flip(PersonGender::getChoices())])
            ->add('birthday', TextType::class, ['required' => false])
            ->add('geoCityName', TextType::class)
            ->add('geoCityId', HiddenType::class)
            ->add('mobile', TextType::class, ['required' => false])
            ->add('phones', TextareaType::class, ['required' => false])
            ->add('email', EmailType::class)
            ->add('password', PasswordType::class)
            ->add('passwordConfirm', PasswordType::class)
            ->add('isMarketingSubscribed', CheckboxType::class, ['required' => false])
            ->add('isHuman', CheckboxType::class)
            ->add('submit', SubmitType::class)
        ;
    }
}
