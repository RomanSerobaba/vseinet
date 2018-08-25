<?php 

namespace AppBundle\Enum;

class ComplaintType
{
    const MANAGER = 'manager';
    const SITE = 'site';
    const DELIVERY_TIME = 'delivery_time';
    const OTHER = 'other';

    public static function getChoices()
    {
        return [
            self::MANAGER => 'работа менеджера',
            self::SITE => 'работа сайта', 
            self::DELIVERY_TIME => 'время доставки', 
            self::OTHER => 'другая',
        ];
    }
}
