<?php

namespace AppBundle\Bus\Catalog\Enum;

class Nofilled
{
    public const DETAILS = 1;
    public const IMAGE = 2;
    public const DESCRIPTION = 3;
    public const MANUFACTURER_LINK = 4;
    public const MANUAL_LINK = 5;

    public static function getChoices(): array
    {
        return [
            self::DETAILS => 'Нет характеристик',
            self::IMAGE => 'Нет изображения',
            self::DESCRIPTION => 'Нет описания',
            self::MANUFACTURER_LINK => 'Нет ссылки на страницу товара',
            self::MANUAL_LINK => 'Нет ссылки на инструкцию',
        ];
    }

    public static function getMnemo(int $key): string
    {
        return self::getMnemos()[$key];
    }

    public static function getMnemos(): array
    {
        return [
            self::DETAILS => 'no_details',
            self::IMAGE => 'no_image',
            self::DESCRIPTION => 'no_description',
            self::MANUFACTURER_LINK => 'no_manufacturer_link',
            self::MANUAL_LINK => 'no_manual_link',
        ];
    }
}
