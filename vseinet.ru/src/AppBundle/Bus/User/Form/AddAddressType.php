<?php 

namespace AppBundle\Bus\User\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\{ CheckboxType, IntegerType, SubmitType, TextType };

class AddAddressType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('address', TextType::class)
            ->add('variant', IntegerType::class, ['required' => false])
            ->add('house', TextType::class)
            ->add('building', TextType::class, ['required' => false])
            ->add('apartment', TextType::class, ['required' => false])
            ->add('floor', IntegerType::class, ['required' => false])
            ->add('hasLift', CheckboxType::class, ['required' => false])
            ->add('comment', TextType::class, ['required' => false])
            ->add('isMain', CheckboxType::class, ['required' => false])
            ->add('submit', SubmitType::class)
        ;
    }
}
