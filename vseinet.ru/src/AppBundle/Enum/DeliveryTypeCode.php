<?php

namespace AppBundle\Enum;

class DeliveryTypeCode
{
    const EX_WORKS = 'ex_works';
    const COURIER = 'courier';
    const POST = 'post';
    const TRANSPORT_COMPANY = 'transport_company';


    public static function getChoices(): array
    {
        return [
            self::EX_WORKS => 'Самовывоз',
            self::COURIER => 'Курьером',
            self::POST => 'Почтой',
            self::TRANSPORT_COMPANY => 'Транспортной компанией',
        ];
    }

    public static function getName(string $code): string
    {
        $choices = self::getChoices();

        if (!isset($choices[$code])) {
            throw new \LogicException(sprintf('Choice "%s" in class "%s" not found.', $code, get_called_class()));
        }

        return $choices[$code];
    }
}
