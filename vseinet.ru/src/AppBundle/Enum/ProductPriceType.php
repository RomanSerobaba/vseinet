<?php

namespace AppBundle\Enum;

class ProductPriceType
{
    public const STANDARD = 'standard';
    public const PRICELIST = 'pricelist';
    public const COMPARED = 'compared';
    public const RECOMMENDED = 'recommended';
    public const MANUAL = 'manual';
    public const ULTIMATE = 'ultimate';
    public const TEMPORARY = 'temporary';
    public const SELLOUT = 'sellout';
    public const ORDERED = 'ordered';

    public static function getChoices()
    {
        return [
            self::MANUAL => 'Ручная цена',
            self::ULTIMATE => 'До последнего',
            self::TEMPORARY => 'Временная цена',
        ];
    }
}
