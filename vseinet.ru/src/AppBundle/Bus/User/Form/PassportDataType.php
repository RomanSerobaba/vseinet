<?php

namespace AppBundle\Bus\User\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\{ TextType, ChoiceType };
use AppBundle\Bus\User\Query\DTO\Passport;
use Symfony\Component\Validator\Constraints as Assert;

class PassportDataType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('seria', TextType::class, ['required' => false,])
            ->add('number', TextType::class, ['required' => false,])
            ->add('issuedAt', TextType::class, ['required' => false,]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Passport::class,
        ]);
    }
}
