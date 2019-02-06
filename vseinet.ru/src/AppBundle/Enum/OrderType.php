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
                self::NATURAL => 'Частное лицо',
                self::LEGAL => 'Организацию',
                self::RETAIL => 'Продажу с магазина',
                self::RESUPPLY => 'Пополнение складских запасов',
                self::CONSUMABLES => 'Покупку расходных материалов',
                // self::EQUIPMENT => 'Покупку оборудования',
            ];
        } else {
            return [
                self::NATURAL => 'Частное лицо',
                self::LEGAL => 'Организацию',
            ];
        }
    }

    public static function isInnerOrder($typeCode)
    {
        return in_array($typeCode, [self::RESUPPLY, self::EQUIPMENT, self::CONSUMABLES]);
    }
}
