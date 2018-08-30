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
            self::MOBILE => 'Мобильный телефон',
            self::PHONE => 'Телефон',
            self::EMAIL => 'Email',
            self::SKYPE => 'Skype',
            self::ICQ => 'ICQ',
        ];
    }

    public static function getTitle($value)
    {
        $choices = self::getChoices();

        return isset($choices[$value]) ? $choices[$value] : '';
    }
}
