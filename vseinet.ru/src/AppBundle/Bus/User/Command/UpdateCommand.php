<?php

namespace AppBundle\Bus\User\Command;

use AppBundle\Bus\Message\Message;
use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Validator\Constraints\Enum;

class UpdateCommand extends Message
{
    /**
     * @Assert\NotBlank(message="Введите Вашу фамилию")
     * @Assert\Type(type="string")
     */
    public $lastname;

    /**
     * @Assert\NotBlank(message="Введите Ваше имя")
     * @Assert\Type(type="string")
     */
    public $firstname;

    /**
     * @Assert\Type(type="string")
     */
    public $secondname;

    /**
     * @Assert\NotBlank(message="Укажите Ваш пол")
     * @Enum("AppBundle\Enum\PersonGender")
     */
    public $gender;

    /**
     * @Assert\Type(type="DateTime")
     */
    public $birthday;

    /**
     * @Assert\NotBlank(message="Укажите Ваш город")
     * @Assert\Type(type="GeoBundle\Entity\GeoCity")
     */
    public $city;
    
    /**
     * @Assert\Type(type="boolean")
     */
    public $isMarketingSubscribed;
}
