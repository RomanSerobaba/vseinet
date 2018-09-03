<?php 

namespace AppBundle\Enum;

class PersonGender
{
    const MALE = 'male';
    const FEMALE = 'female';

    public static function getChoices(): array 
    {
        return [
            self::MALE => 'мужской',
            self::FEMALE => 'женский',
        ];
    }

    public static function getName(string $value): string
    {
        $choices = self::getChoices();

        if (!isset($choices[$value])) {
            throw new \LogicException(strintf('Choice "%s" in class "%s" not found.', $value, get_called_class()));
        }
        
        return $choices[$value];
    }
}
