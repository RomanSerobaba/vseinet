<?php

namespace AppBundle\Enum;

/**
 * @deprecated
 */
class OrderTypeCode
{
    public const SITE = 'site';
    public const SHOP = 'shop';
    public const LEGAL = 'legal';
    public const EQUIPMENT = 'equipment';
    public const RESUPPLY = 'resupply';
    public const REQUEST = 'request';

    public static function isClient($code)
    {
        switch ($code) {
            case self::SITE:
            case self::SHOP:
            case self::LEGAL:
            case self::REQUEST:
                return true;
            default:
                return false;
        }
    }
}
