<?php

namespace AppBundle\Twig\Extension;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class DiscountExtension extends AbstractExtension
{
    const DISCOUNT_PERCENTS = [
           5000 => 50,
          10000 => 48,
          15000 => 46,
          20000 => 44,
          25000 => 42,
          30000 => 40,
          35000 => 38,
          40000 => 36,
          50000 => 24,
          60000 => 32,
          70000 => 30,
          80000 => 29,
          90000 => 28,
         100000 => 27,
         200000 => 26,
         300000 => 25,
         400000 => 24,
         500000 => 23,
         600000 => 22,
         700000 => 21,
         800000 => 20,
         900000 => 19,
        1000000 => 18,
        1200000 => 17,
        1400000 => 16,
        1600000 => 15,
        1800000 => 14,
        2000000 => 13,
        2500000 => 12,
        3000000 => 11,
    ];

    /**
     * {@inheritdoc}
     */
    public function getFilters()
    {
        return [
            new TwigFilter('discount_percent', [$this, 'getDiscountPercent']),
            new TwigFilter('discount_price', [$this, 'getDiscountPrice']),
        ];
    }

    public function getDiscountPercent($price)
    {
        foreach (self::DISCOUNT_PERCENTS as $amount => $percent) {
            if ($amount > $price) {
                return $percent;
            }
        }

        return 10;
    }

    public function getDiscountPrice($price)
    {
        return round($price * 100 / (100 - $this->getDiscountPercent($price)), -2);
    }
}
