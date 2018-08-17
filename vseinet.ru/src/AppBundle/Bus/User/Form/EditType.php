<?php 

namespace AppBundle\Bus\User\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\{ CheckboxType, ChoiceType, DateType, TextType, SubmitType };
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use AppBundle\Enum\PersonGender;
use AppBundle\Entity\GeoCity;
use AppBundle\Repository\GeoCityRepository;

class EditType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('lastname', TextType::class)
            ->add('firstname', TextType::class)
            ->add('secondname', TextType::class, ['required' => false])
            ->add('gender', ChoiceType::class, ['expanded' => true, 'choices' => PersonGender::getChoices()])
            ->add('birthday', DateType::class, [
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
            ->add('isMarketingSubscribed', CheckboxType::class, ['required' => false])
            ->add('submit', SubmitType::class)
        ;
    }
}
