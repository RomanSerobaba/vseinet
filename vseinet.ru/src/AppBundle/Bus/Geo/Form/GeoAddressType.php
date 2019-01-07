<?php

namespace AppBundle\Bus\Geo\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\{ TextType, CheckboxType, HiddenType };
use AppBundle\Bus\Geo\Query\DTO\Address;
use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Entity\GeoStreet;

class GeoAddressType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        if (!empty($options['data']->geoStreetId)) {
            $geoStreet = $this->em->getRepository(GeoStreet::class)->find($geoStreetId);
            $geoStreetName = $geoStreet->getName();
        } else {
            $geoStreetName = NULL;
        }

        $builder
            ->add('geoStreetId', HiddenType::class, ['required' => false,])
            ->add('geoStreetName', TextType::class, ['required' => false, 'data' => $geoStreetName,])
            ->add('house', TextType::class, ['required' => false,])
            ->add('building', TextType::class, ['required' => false,])
            ->add('apartment', TextType::class, ['required' => false,])
            ->add('floor', TextType::class, ['required' => false,])
            ->add('hasLift', CheckboxType::class, ['required' => false,])
            ->add('office', TextType::class, ['required' => false,])
            ->add('postalCode', TextType::class, ['required' => false,]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Address::class,
        ]);
    }
}
