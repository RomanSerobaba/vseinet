<?php 

namespace AppBundle\Bus\User\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type;

class RegistrType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $gender = [
            'Мужской' => 'male',
            'Женский' => 'female',
        ];

        $builder
            ->add('lastname', Type\TextType::class, ['required' => true])
            ->add('firstname', Type\TextType::class, ['required' => true])
            ->add('secondname', Type\TextType::class, ['required' => false])
            ->add('gender', Type\ChoiceType::class, ['required' => true, 'expanded' => true, 'choices' => $gender])
            ->add('birthday', Type\TextType::class, ['required' => false])
            ->add('city', Type\TextType::class, ['required' => true])
            ->add('mobile', Type\TextType::class, ['required' => false])
            ->add('phones', Type\TextareaType::class, ['required' => false])
            ->add('email', Type\EmailType::class, ['required' => true])
            ->add('password', Type\PasswordType::class, ['required' => true])
            ->add('passwordConfirm', Type\PasswordType::class, ['required' => true])
            ->add('isMarketingSubscribed', Type\CheckboxType::class, ['required' => false])
            ->add('isHuman', Type\CheckboxType::class, ['required' => true])
            ->add('submit', Type\SubmitType::class)
        ;
    }
}
