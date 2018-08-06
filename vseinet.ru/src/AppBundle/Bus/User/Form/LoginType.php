<?php 

namespace AppBundle\Bus\User\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type;
use AppBundle\Bus\User\Command\LoginCommand;

class LoginType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('username', Type\TextType::class, ['required' => true])
            ->add('password', Type\PasswordType::class, ['required' => true])
            ->add('remember', Type\CheckboxType::class, ['required' => false])
            ->add('submit', Type\SubmitType::class)
        ;
    }
}
