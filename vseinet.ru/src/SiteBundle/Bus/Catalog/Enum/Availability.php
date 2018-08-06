<?php 

namespace SiteBundle\Bus\Catalog\Enum;

class Availability
{
    const AVAILABLE = 1;
    const ON_DEMAND = 2;
    const ACTIVE = 3;
    const FOR_ALL_TIME = 4;

    public static function getOptions(bool $isEmployee): array
    {
        $options =  [
            self::ACTIVE => 'все',
            self::AVAILABLE => 'есть в магазине',
            self::ON_DEMAND => 'есть в магазине и под заказ',
        ];
        if ($isEmployee) {
            $options[self::FOR_ALL_TIME] = 'товар за все время';
        }

        return $options;
    }
}
