<?php

namespace AppBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;

class HiddenIntTransformer implements DataTransformerInterface
{
    public function transform($value)
    {
        return $value;
    }

    public function reverseTransform($value)
    {
        return false === ($int = filter_var($value, FILTER_VALIDATE_INT)) ? null : $int;
    }
}
