<?php

namespace AppBundle\Bus\User\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\{ CheckboxType, ChoiceType, IntegerType, SubmitType, TextType };

class CreateAddressType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        // print_r($options['data']);exit;
        $builder
            ->add('address', TextType::class, [
                'required' => true,
            ])
            ->add('variant', ChoiceType::class, [
                'required' => true,
                'choices' => array_flip($options['data']->variants ?? []),
                'expanded' => true,
            ])
            ->add('house', TextType::class, [
                'required' => true,
            ])
            ->add('building', TextType::class,[
                'required' => false,
            ])
            ->add('apartment', TextType::class, [
                'required' => false,
            ])
            ->add('floor', IntegerType::class, [
                'required' => false,
            ])
            ->add('hasLift', CheckboxType::class, [
                'required' => false,
            ])
            ->add('comment', TextType::class, [
                'required' => false,
            ])
            ->add('isMain', CheckboxType::class, [
                'required' => false,
            ])
            ->add('submit', SubmitType::class)
        ;
    }
}
