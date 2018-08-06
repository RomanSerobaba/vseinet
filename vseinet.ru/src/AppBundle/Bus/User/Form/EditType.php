<?php 

namespace AppBundle\Bus\User\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type;

class EditType extends AbstractType
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
            ->add('birthday', Type\DateType::class, ['required' => false, 'widget' => 'single_text', 'html5' => false, 'format' => 'dd.MM.yyyy'])
            // ->add('cityId', Type\HiddenType::class, ['required' => true])
            ->add('isMarketingSubscribed', Type\CheckboxType::class, ['required' => false])
            ->add('submit', Type\SubmitType::class)
        ;
    }
}
