<?php 

namespace AppBundle\Bus\Geo\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Doctrine\ORM\EntityManagerInterface;
use AppBundle\Entity\GeoCity;

class GeoCityType extends AbstractType
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
        if (empty($options['choices'])) {
            $geoCities = $this->em->getRepository(GeoCity::class)->findBy(['unit' => 'г'], ['name' => 'ASC']);
            foreach ($geoCities as $geoCity) {
                $options['choices'][$geoCity->getName()] = $geoCity->getId();
            }
        } 
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefault('choices_as_values', true); 
    }

    public function getParent()
    {
        return ChoiceType::class;
    }
}
