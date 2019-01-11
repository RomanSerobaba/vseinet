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
            'Самовывоз' => self::EX_WORKS,
            'Курьером' => self::COURIER,
            'Почтой' => self::POST,
            'Транспортной компанией' => self::TRANSPORT_COMPANY,
        ];
    }

    public static function getName(string $code): string
    {
        $choices = self::getChoices();

        if (!isset($choices[$code])) {
            throw new \LogicException(strintf('Choice "%s" in class "%s" not found.', $code, get_called_class()));
        }

        return $choices[$code];
    }
}
