<?php

namespace AppBundle\Bus\User\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use AppBundle\Form\Type\HiddenIntType;
use AppBundle\Enum\PersonGender;
use AppBundle\Bus\User\Command\AccountEditCommand;

class AccountEditType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('lastname', TextType::class)
            ->add('firstname', TextType::class)
            ->add('secondname', TextType::class, ['required' => false])
            ->add('gender', ChoiceType::class, ['expanded' => true, 'choices' => array_flip(PersonGender::getChoices())])
            ->add('birthday', DateType::class, [
                'required' => false,
                'widget' => 'single_text',
                'html5' => false,
                'format' => 'dd.MM.yyyy',
            ])
            ->add('geoCityName', TextType::class)
            ->add('geoCityId', HiddenIntType::class)
            ->add('isMarketingSubscribed', CheckboxType::class, ['required' => false])
            ->add('submit', SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => AccountEditCommand::class,
        ]);
    }
}
