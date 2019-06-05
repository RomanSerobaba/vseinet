<?php

namespace AppBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;

class PriceTransformer implements DataTransformerInterface
{
    public function transform($value)
    {
        if (null === $value || '' === $value) {
            return '';
        }

        $pennies = intval($value);
        $rubles = intdiv($pennies, 100);
        $pennies = $pennies - 100 * $rubles;

        $result = strval($rubles);
        if ($pennies) {
            $result .= ',';
            if (10 > $pennies) {
                $result .= '0';
            }
            $result .= strval($pennies);
        }

        return $result;
    }

    public function reverseTransform($value)
    {
        if (null === $value || '' === $value) {
            return null;
        }

        return intval(round(100 * floatval(str_replace([' ', ','], ['', '.'], $value))));
    }
}
