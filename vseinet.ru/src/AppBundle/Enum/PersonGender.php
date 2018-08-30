<?php 

namespace AppBundle\Enum;

class PersonGender
{
    const MALE = 'male';
    const FEMALE = 'female';

    public static function getChoices()
    {
        return [
            self::MALE => 'мужской',
            self::FEMALE => 'женский',
        ];
    }

    public static function getTitle($value)
    {
        $choices = self::getChoices();
        
        return isset($choices[$value]) ? $choices[$value] : '';
    }
}
