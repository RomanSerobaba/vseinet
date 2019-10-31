<?php

namespace AppBundle\Enum;

class ProductPriceTypeCode
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
            self::TEMPORARY => 'Временная цена',
            self::ULTIMATE => 'До последнего',
            self::MANUAL => 'Ручная цена',
        ];
    }

    public static function getName($typeCode)
    {
        switch ($typeCode) {
            case self::STANDARD: return 'стандартная наценка';
            case self::PRICELIST: return 'наценка по поставщику';
            case self::COMPARED: return 'сравненная с конкурентом';
            case self::RECOMMENDED: return 'РИЦ';
            case self::MANUAL: return 'ручная цена';
            case self::ULTIMATE: return 'цена до последнего остатка';
            case self::TEMPORARY: return 'временная цена';
            case self::SELLOUT: return 'распродажа';
            case self::ORDERED: return 'цена из пока непереданного заказа';
        }
    }
}
