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
    const WHOLESALE = 'wholesale';

    public static function getChoices($forEmployee = false, $isFranchiser = false)
    {
        if ($forEmployee) {
            $data = [
                self::NATURAL => 'Частное лицо',
                self::LEGAL => 'Организацию',
                self::RETAIL => 'Продажу с магазина',
                self::RESUPPLY => 'Пополнение магазина (Акция)',
                self::CONSUMABLES => 'Покупку расходных материалов',
                // self::EQUIPMENT => 'Покупку оборудования',
            ];

            if ($isFranchiser) {
                $data[self::WHOLESALE] = 'Оптовый';
            }

            return $data;
        } else {
            return [
                self::NATURAL => 'Частное лицо',
                self::LEGAL => 'Организацию',
            ];
        }
    }

    public static function isInnerOrder($typeCode)
    {
        return in_array($typeCode, [self::RESUPPLY, self::EQUIPMENT, self::CONSUMABLES, self::WHOLESALE]);
    }
}
