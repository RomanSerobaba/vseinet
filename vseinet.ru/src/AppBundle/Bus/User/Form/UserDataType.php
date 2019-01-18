<?php

namespace AppBundle\Bus\User\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\{ EmailType, TextType, HiddenType };
use AppBundle\Form\Type\PhoneType;
use AppBundle\Bus\User\Query\DTO\UserData;
use Symfony\Component\Validator\Constraints as Assert;

class UserDataType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('position', TextType::class)
            ->add('fullname', TextType::class)
            ->add('phone', PhoneType::class, [
                'required' => false])
            ->add('additionalPhone', PhoneType::class, [
                'required' => false])
            ->add('email', TextType::class, ['required' => false])
            ->add('userId', HiddenType::class, ['required' => false])
            ->add('comuserId', HiddenType::class, ['required' => false])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => UserData::class,
            'constraints' => [
                new Assert\Callback(function($data, $context){
                    if (empty($data->phone) && empty($data->additionalPhone)) {
                        $context->buildViolation('Необходимо заполнить хотя бы один контактный номер (основной или дополнительный)')
                            ->atPath('phone')
                            ->addViolation();
                        }
                })],
        ]);
    }
}
