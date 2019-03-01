<?php

namespace AppBundle\Bus\Catalog\Enum;

class Nofilled
{
    const DETAILS = 'no_details';
    const IMAGE = 'no_image';
    const DESCRIPTION = 'no_description';
    const MANUFACTURER_LINK = 'no_manufacturer_link';
    const MANUAL_LINK = 'no_manual_link';

    public function getChoices(): array
    {
        return [
            self::DETAILS => 'Нет характеристик',
            self::IMAGE => 'Нет изображений',
            self::DESCRIPTION => 'Нет описания',
            self::MANUFACTURER_LINK => 'Нет ссылки на страницу товара',
            self::MANUAL_LINK => 'Нет ссылки на инструкцию',
        ];
    }
}
