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

    public static function getChoices(): array
    {
        return [
            self::MOBILE => 'Мобильный телефон',
            self::PHONE => 'Телефон',
            self::EMAIL => 'Email',
            self::SKYPE => 'Skype',
            self::ICQ => 'ICQ',
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
