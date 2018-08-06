<?php 

namespace AppBundle\Bus\User\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type;

class ChangePasswordType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('password', Type\PasswordType::class, ['required' => true])
            ->add('newPassword', Type\PasswordType::class, ['required' => true])
            ->add('newPasswordConfirm', Type\PasswordType::class, ['required' => true])
            ->add('submit', Type\SubmitType::class)
        ;
    }
}