<?php

namespace AdminBundle\Bus\Product\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use AppBundle\Form\Type\PriceType;
use AdminBundle\Bus\Product\Command\SetPriceCommand;
use AppBundle\Enum\ProductPriceType;

class SetPriceFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            // ->add('id', HiddenType::class)
            ->add('price', PriceType::class)
            ->add('type', ChoiceType::class, ['choices' => array_flip(ProductPriceType::getChoices())])
            ->add('submit', SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => SetPriceCommand::class,
        ]);
    }
}
