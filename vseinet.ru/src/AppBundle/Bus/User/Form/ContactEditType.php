<?php 

namespace AppBundle\Bus\User\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type;
use AppBundle\Enum\ContactTypeCode;

class ContactEditType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('typeCode', Type\ChoiceType::class, [
                'required' => true,
                'choices' => ContactTypeCode::getChoices(),
            ])
            ->add('value', Type\TextType::class, [
                'required' => true,
            ])
            ->add('comment', Type\TextType::class, [
                'required' => false,
            ])
            ->add('isMain', Type\CheckboxType::class, [
                'required' => false,
            ])
            ->add('submit', Type\SubmitType::class)
        ;
    }
}
