<?php

namespace AppBundle\Bus\Order\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use AppBundle\Bus\User\Form\UserDataType;
use AppBundle\Bus\User\Form\IsHumanType;
use AppBundle\Bus\Order\Command\ReceiptsOfProductCommand;

class ReceiptsOfProductFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $trackingPeriods = [
            'Одна неделя' => 7,
            'Две недели' => 14,
            'Один месяц' => 30,
        ];

        $builder
            ->add('userData', UserDataType::class, ['additional_phone' => false, 'email_required' => true])
            ->add('trackingPeriod', ChoiceType::class, ['choices' => $trackingPeriods])
            ->add('isHuman', IsHumanType::class)
            ->add('submit', SubmitType::class);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ReceiptsOfProductCommand::class,
        ]);
    }
}
