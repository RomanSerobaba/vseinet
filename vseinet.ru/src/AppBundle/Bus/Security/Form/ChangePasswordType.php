<?php 

namespace AppBundle\Bus\Security\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\{ PasswordType, SubmitType };

class ChangePasswordType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('password', PasswordType::class)
            ->add('newPassword', PasswordType::class)
            ->add('newPasswordConfirm', PasswordType::class)
            ->add('submit', SubmitType::class)
        ;
    }
}