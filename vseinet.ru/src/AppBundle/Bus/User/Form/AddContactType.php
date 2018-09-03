<?php 

namespace AppBundle\Bus\User\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\{ CheckboxType, ChoiceType, HiddenType, SubmitType, TextType };
use AppBundle\Enum\ContactTypeCode;

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
            ;
        }
        $builder
            ->add('value', TextType::class)
            ->add('comment', TextType::class, ['required' => false])
            ->add('isMain', CheckboxType::class, ['required' => false])
            ->add('submit', SubmitType::class)
        ;
    }
}
