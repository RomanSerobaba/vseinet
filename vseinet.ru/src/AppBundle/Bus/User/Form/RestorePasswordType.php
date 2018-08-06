<?php 

namespace AppBundle\Bus\User\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type;

class RestorePasswordType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('password', Type\PasswordType::class, ['required' => true])
            ->add('passwordConfirm', Type\PasswordType::class, ['required' => true])
            ->add('submit', Type\SubmitType::class)
        ;
    }
}