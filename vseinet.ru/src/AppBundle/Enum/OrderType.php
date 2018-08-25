<?php 

namespace AppBundle\Enum;

class OrderType
{
    const NATURAL = 'Заказ на физ. лицо';
    const LEGAL = 'Заказ на юр. лицо';
    const RETAIL = 'Розничная продажа';
    const RESUPPLY = 'Пополнение складских запасов';
    const CONSUMABLES = 'Покупка расходных материалов';
    const EQUIPMENT = 'Покупка оборудования';
}