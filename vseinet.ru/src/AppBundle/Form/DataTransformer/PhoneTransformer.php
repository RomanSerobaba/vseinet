<?php 

namespace AppBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;

class PhoneTransformer implements DataTransformerInterface
{
    public function transform($value) 
    {
        switch (strlen($value)) {
            case 6:
                sscanf($value, '%2s%2s%2s', $e1, $e2, $e3);
                $value = "{$e1}-{$e2}-{$e3}";
                break;

            case 7:
                sscanf($value, '%3s%2s%2s', $e1, $e2, $e3);
                $value = "{$e1}-{$e2}-{$e3}";
                break;

            case 10:
                sscanf($value, "%3s%3s%2s%2s", $area, $e1, $e2, $e3);
                $value = "+7 ({$area}) {$e1}-{$e2}-{$e3}";
                break;
        }

        return $value;
    }

    public function reverseTransform($value)
    {
        $value = preg_replace('/\D+/u', '', $value);
        if (11 === strlen($value)) {
            $value = substr($value, 1);
        }

        return $value;
    }
}
