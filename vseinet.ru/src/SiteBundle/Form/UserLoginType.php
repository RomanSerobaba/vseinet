<?php

namespace SiteBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type;

class UserLoginType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username', Type\TextType::class, ['required' => true])
            ->add('password', Type\PasswordType::class, ['required' => true])
            ->add('remember', Type\CheckboxType::class, ['required' => false])
            ->add('submit', Type\SubmitType::class)
        ;
    }
}