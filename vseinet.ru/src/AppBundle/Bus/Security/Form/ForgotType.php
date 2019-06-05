<?php

namespace AppBundle\Bus\Security\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use AppBundle\Bus\User\Form\IsHumanType;

class ForgotType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('username', TextType::class)
            ->add('isHuman', IsHumanType::class)
            ->add('submit', SubmitType::class)
        ;
    }
}
