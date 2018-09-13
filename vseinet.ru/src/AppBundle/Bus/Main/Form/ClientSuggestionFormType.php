<?php 

namespace AppBundle\Bus\Main\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\{ SubmitType, TextareaType };
use AppBundle\Bus\User\Form\UserDataType;
use AppBundle\Bus\User\Form\IsHumanType;
use AppBundle\Bus\Main\Command\ClientSuggestionCommand;

class ClientSuggestionFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('text', TextareaType::class)
            ->add('userData', UserDataType::class)
            ->add('isHuman', IsHumanType::class)
            ->add('submit', SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ClientSuggestionCommand::class,
        ]);
    }
}
