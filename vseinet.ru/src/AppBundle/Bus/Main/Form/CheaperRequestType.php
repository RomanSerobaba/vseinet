<?php 

namespace AppBundle\Bus\Main\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\{ SubmitType, TextareaType, TextType };
use Doctrine\ORM\EntityManagerInterface;
use AppBundle\Form\Type\PriceType;
use AppBundle\Bus\Geo\Form\GeoCityType;
use AppBundle\Bus\User\Form\UserDataType;
use AppBundle\Bus\User\Form\IsHumanType;
use AppBundle\Bus\Main\Command\CheaperRequestCommand;

class CheaperRequestType extends AbstractType
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
        $q = $this->em->createQuery("
            SELECT gc 
            FROM AppBundle:GeoCity AS gc 
            INNER JOIN AppBundle:GeoPoint AS gp WITH gp.geoCityId = gc.id 
            INNER JOIN AppBundle:Representative AS r WITH r.geoPointId = gp.id 
            WHERE r.isActive = true AND gc.geoAreaId IS NULL AND gc.unit = 'г'
            ORDER BY gc.name
        ");
        $geoCities = $q->getResult();

        $choicesGeoCities = [];
        foreach ($geoCities as $geoCity) {
            $choicesGeoCities[$geoCity->getName()] = $geoCity->getId();
        }

        $builder
            ->add('competitorPrice', PriceType::class)
            ->add('competitorLink', TextType::class)
            ->add('geoCityId', GeoCityType::class, ['choices' => $choicesGeoCities])
            ->add('userData', UserDataType::class)
            ->add('comment', TextareaType::class, ['required' => false])
            ->add('isHuman', IsHumanType::class)
            ->add('submit', SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => CheaperRequestCommand::class,
        ]);
    }
}
