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
}
