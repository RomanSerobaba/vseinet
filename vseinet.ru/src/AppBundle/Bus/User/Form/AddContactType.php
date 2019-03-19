<?php

namespace AppBundle\Bus\User\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use AppBundle\Enum\ContactTypeCode;
use AppBundle\Bus\User\Command\AddContactCommand;

class AddContactType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        if ($options['data']->id) {
            $builder
                ->add('typeCode', HiddenType::class)
                ->add('typeCodeName', TextType::class, ['required' => false])
            ;
        } else {
            $builder
                ->add('typeCode', ChoiceType::class, ['choices' => array_flip(ContactTypeCode::getChoices())])
                ->add('typeCodeName', HiddenType::class)
            ;
        }
        $builder
            ->add('value', TextType::class)
            ->add('comment', TextType::class, ['required' => false])
            ->add('isMain', CheckboxType::class, ['required' => false])
            ->add('submit', SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => AddContactCommand::class,
        ]);
    }
}
