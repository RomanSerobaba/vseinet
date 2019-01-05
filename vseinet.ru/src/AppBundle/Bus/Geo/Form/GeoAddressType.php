<?php

namespace AppBundle\Bus\Geo\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\{ TextType, ChoiceType };
use AppBundle\Bus\Geo\Query\DTO\Address;
use Symfony\Component\Validator\Constraints as Assert;

class GeoAddressType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, ['required' => false,])
            ->add('legalAddress', TextType::class, ['required' => false,])
            ->add('settlementAccount', TextType::class, ['required' => false,])
            ->add('tin', TextType::class, ['required' => false,])
            ->add('kpp', TextType::class, ['required' => false,])
            ->add('bic', TextType::class, ['required' => false,])
            ->add('withVat', ChoiceType::class, [
                'choices' => ['Приобрести товар без НДС' => false, 'Приобрести товар с НДС' => true,],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Address::class,
        ]);
    }
}
