<?php 

namespace AppBundle\Bus\User\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use AppBundle\Enum\PersonGender;
use GeoBundle\Entity\GeoCity;
use GeoBundle\Repository\GeoCityRepository;

class EditType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('lastname', Type\TextType::class, [
                'required' => true,
            ])
            ->add('firstname', Type\TextType::class, [
                'required' => true,
            ])
            ->add('secondname', Type\TextType::class, [
                'required' => false,
            ])
            ->add('gender', Type\ChoiceType::class, [
                'required' => true, 
                'expanded' => true, 
                'choices' => PersonGender::getChoices(),
            ])
            ->add('birthday', Type\DateType::class, [
                'required' => false, 
                'widget' => 'single_text', 
                'html5' => false, 
                'format' => 'dd.MM.yyyy',
            ])
            ->add('city', EntityType::class, [
                'class' => GeoCity::class,
                'query_builder' => function(GeoCityRepository $repo) {
                    return $repo->createQueryBuilder('gc')->where('gc.unit = \'г\'')->orderBy('gc.name', 'ASC');
                },
                'choice_label' => 'name',
                'choice_value' => 'id',
            ])
            ->add('isMarketingSubscribed', Type\CheckboxType::class, [
                'required' => false,
            ])
            ->add('submit', Type\SubmitType::class)
        ;
    }
}
