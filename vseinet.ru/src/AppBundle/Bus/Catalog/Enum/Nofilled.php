<?php

namespace AppBundle\Bus\Catalog\Enum;

class Nofilled
{
    public const NO_DETAILS = 1;
    public const PARTIAL_DETAILS = 2;
    public const NO_IMAGE = 3;
    public const NO_DESCRIPTION = 4;
    public const NO_MANUFACTURER_LINK = 5;
    public const NO_MANUAL_LINK = 6;

    public static function getChoices(): array
    {
        return [
            self::NO_DETAILS => 'Нет характеристик',
            self::PARTIAL_DETAILS => 'Не все характеристики',
            self::NO_IMAGE => 'Нет изображения',
            self::NO_DESCRIPTION => 'Нет описания',
            self::NO_MANUFACTURER_LINK => 'Нет ссылки на страницу товара',
            self::NO_MANUAL_LINK => 'Нет ссылки на инструкцию',
        ];
    }

    public static function getMnemo(int $key): string
    {
        return self::getMnemos()[$key];
    }

    public static function getMnemos(): array
    {
        return [
            self::NO_DETAILS => 'no_details',
            self::PARTIAL_DETAILS => 'partial_details',
            self::NO_IMAGE => 'no_image',
            self::NO_DESCRIPTION => 'no_description',
            self::NO_MANUFACTURER_LINK => 'no_manufacturer_link',
            self::NO_MANUAL_LINK => 'no_manual_link',
        ];
    }
}
