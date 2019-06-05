<?php

namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use AppBundle\Form\DataTransformer\HiddenIntTransformer;

class HiddenIntType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addModelTransformer(new HiddenIntTransformer());
    }

    public function getParent()
    {
        return HiddenType::class;
    }
}
