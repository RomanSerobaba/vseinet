<?php 

namespace AppBundle\Enum;

class ContactTypeCode
{
    const MOBILE = 'mobile';
    const PHONE = 'phone';
    const EMAIL = 'email';
    const SKYPE = 'skype';
    const ICQ = 'icq';
    const CUSTOM = 'custom';

    public static function getChoices()
    {
        return [
            'Мобильный телефон' => self::MOBILE,
            'Телефон' => self::PHONE,
            'Email' => self::EMAIL,
            'Skype' => self::SKYPE,
            'ICQ' => self::ICQ,
        ];
    }

    public static function getTitle($value)
    {
        $choices = array_flip(self::getChoices());

        return isset($choices[$value]) ? $choices[$value] : '';
    }
}
