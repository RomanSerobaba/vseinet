<?php

namespace AppBundle\Bus\Order\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\{ TextType, ChoiceType, HiddenType };
use AppBundle\Bus\Order\Command\Schema\OrganizationDetails;
use Symfony\Component\Validator\Constraints as Assert;

class OrganizationDetailsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, ['required' => false,])
            ->add('legalAddress', TextType::class, ['required' => false,])
            ->add('settlementAccount', TextType::class, ['required' => false,])
            ->add('tin', TextType::class, ['required' => false,])
            ->add('kpp', TextType::class, ['required' => false,])
            ->add('bic', TextType::class, ['required' => false,])
            ->add('bankId', HiddenType::class, ['required' => false,])
            ->add('bankName', TextType::class, ['required' => false,])
            ->add('withVat', ChoiceType::class, [
                'choices' => ['Приобрести товар без НДС' => false, 'Приобрести товар с НДС' => true,],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => OrganizationDetails::class,
            'constraints' => [
                new Assert\Callback(function($data, $context){
                    if (!empty($data->settlementAccount) && empty($data->bankId)) {
                        $context->buildViolation('Необходимо указать БИК для расчетного счета')
                            ->atPath('bic')
                            ->addViolation();
                        }
                })],
        ]);
    }
}
