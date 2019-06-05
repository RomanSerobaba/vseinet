<?php

namespace AppBundle\Bus\User\Command;

use AppBundle\Bus\Message\Message;
use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Validator\Constraints\Enum;

class AccountEditCommand extends Message
{
    /**
     * @Assert\NotBlank(message="Введите Вашу фамилию")
     * @Assert\Type("string")
     */
    public $lastname;

    /**
     * @Assert\NotBlank(message="Введите Ваше имя")
     * @Assert\Type("string")
     */
    public $firstname;

    /**
     * @Assert\Type("string")
     */
    public $secondname;

    /**
     * @Assert\NotBlank(message="Укажите Ваш пол")
     * @Enum("AppBundle\Enum\PersonGender")
     */
    public $gender;

    /**
     * @Assert\DateTime
     */
    public $birthday;

    /**
     * @Assert\NotBlank(message="Укажите Ваш город")
     * @Assert\Type("string")
     */
    public $geoCityName;

    /**
     * @Assert\Type("integer")
     */
    public $geoCityId;

    /**
     * @Assert\Type("boolean")
     */
    public $isMarketingSubscribed;
}
