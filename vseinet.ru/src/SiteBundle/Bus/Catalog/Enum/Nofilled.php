<?php 

namespace SiteBundle\Bus\Catalog\Enum;

class Nofilled
{
    const DETAILS = 1;
    const IMAGES = 2;
    const DESCRIPTION = 3;
    const MANUFACTURER_LINK = 4;
    const MANUAL_LINK = 5;

    public static function getOptions(): array
    {
        return [
            self::DETAILS => 'Нет характеристик',
            self::IMAGES => 'Нет изображений',
            self::DESCRIPTION => 'Нет описания',
            self::MANUFACTURER_LINK => 'Нет ссылки на страницу товара',
            self::MANUAL_LINK => 'Нет ссылки на инструкцию',
        ];
    }
}
