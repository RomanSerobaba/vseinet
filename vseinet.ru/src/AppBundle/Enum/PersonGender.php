<?php 

namespace AppBundle\Enum;

class PersonGender
{
    const MALE = 'male';
    const FEMALE = 'female';

    public static function getChoices()
    {
        return [
            'мужской' => self::MALE,
            'женский' => self::FEMALE,
        ];
    }

    public static function getTitle($value)
    {
        $choices = array_flip(self::getChoices());
        
        return isset($choices[$value]) ? $choices[$value] : '';
    }
}
