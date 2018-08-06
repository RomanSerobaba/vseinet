<?php 

namespace AppBundle\Twig\Extension;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class PriceFormatExtension extends AbstractExtension
{
    /**
     * {@inheritdoc}
     */
    public function getFilters()
    {
        return array(
            new TwigFilter('price_format', [$this, 'format']),
        );
    }

    /**
     * Returns formated price value.
     * 
     * @param integer $value 
     * @param array $paramsters
     *  
     * @return string
     */
    public function format($value, array $parameters = [])
    {
        if (!$value && '0' !== (string) $value) {
            return '';
        }

        $decimals = $parameters['decimals'] ?? null;
        $decimalSeparator = $parameters['ds'] ?? '.';
        $thousandsSeparator = $parameters['ts'] ?? ' ';

        $value = intval($value);
        $sign = 0 > $value ? '-' : '';
        $value = abs($value);

        $rubles = null !== $decimals && 0 >= $decimals ? round($value / 100) : floor($value / 100);
        $formated = $sign.number_format($rubles, 0, $decimalSeparator, $thousandsSeparator);

        if ($hideZeroPennies = null === $decimals) {
            $decimals = 2;
        } else {
            $decimals = intval($decimals);
            if (2 < $decimals) {
                $decimals = 2;
            }
        }
        if (0 < $decimals) {
            $pennies = $value % 100;
            if ($pennies || !$hideZeroPennies) {
                if (1 == $decimals) {
                    $pennies = round($pennies / 10);
                }
                $formated .= $decimalSeparator.sprintf("%'.0{$decimals}d", $pennies);
            }
        }

        return $formated;
    }
}
