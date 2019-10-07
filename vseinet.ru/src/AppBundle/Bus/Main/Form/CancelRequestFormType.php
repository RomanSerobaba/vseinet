<?php

namespace AppBundle\Bus\Main\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use AppBundle\Bus\User\Form\IsHumanType;
use AppBundle\Bus\Main\Command\CancelRequestCommand;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;

class CancelRequestFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('id', HiddenType::class)
            ->add('comment', TextareaType::class)
            ->add('isHuman', IsHumanType::class)
            ->add('submit', SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => CancelRequestCommand::class,
        ]);
    }
}
