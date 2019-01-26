<?php

namespace AppBundle\Bus\Order\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\{ TextType, CheckboxType, HiddenType };
use AppBundle\Bus\Order\Query\DTO\Address;
use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Entity\GeoStreet;
use Doctrine\ORM\EntityManagerInterface;

class AddressType extends AbstractType
{
    /**
     * @var EntityManagerInterface
     */
    protected $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        if (!empty($options['data']->geoStreetId)) {
            $geoStreet = $this->em->getRepository(GeoStreet::class)->find($options['data']->geoStreetId);

            if ($geoStreet instanceof GeoStreet) {
                $geoStreetName = $geoStreet->getName();
            } else {
                $geoStreetName = $options['data']->geoStreetId = NULL;
            }
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
            // 'compound' => true,
        ]);
    }
}
