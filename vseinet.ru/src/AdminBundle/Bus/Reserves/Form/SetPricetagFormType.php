<?php

namespace AdminBundle\Bus\Reserves\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use AppBundle\Form\Type\PriceType;
use AdminBundle\Bus\Reserves\Command\SetPricetagCommand;
use AppBundle\Form\Type\HiddenIntType;

class SetPricetagFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('price', PriceType::class)
            ->add('geoPointId', HiddenIntType::class)
            ->add('submit', SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => SetPricetagCommand::class,
        ]);
    }
}
