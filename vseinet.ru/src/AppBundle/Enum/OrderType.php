<?php

namespace AppBundle\Enum;

class OrderType
{
    const NATURAL = 'natural';
    const LEGAL = 'legal';
    const RETAIL = 'retail';
    const RESUPPLY = 'resupply';
    const CONSUMABLES = 'consumables';
    const EQUIPMENT = 'equipment';

    public static function getChoices($forEmployee = false)
    {
        if ($forEmployee) {
            return [
                self::NATURAL => 'Заказ на физ. лицо',
                self::LEGAL => 'Заказ на юр. лицо',
                self::RETAIL => 'Продажа с магазина',
                self::RESUPPLY => 'Пополнение складских запасов',
                self::CONSUMABLES => 'Покупка расходных материалов',
                // self::EQUIPMENT => 'Покупка оборудования',
            ];
        } else {
            return [
                self::NATURAL => 'Заказ на физ. лицо',
                self::LEGAL => 'Заказ на юр. лицо',
            ];
        }
    }

    public static function isInnerOrder($typeCode)
    {
        return in_array($typeCode, [self::RESUPPLY, self::EQUIPMENT, self::CONSUMABLES]);
    }
}
