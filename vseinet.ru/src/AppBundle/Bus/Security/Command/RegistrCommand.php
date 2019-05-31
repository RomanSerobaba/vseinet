<?php

namespace AppBundle\Bus\Security\Command;

use AppBundle\Bus\Message\Message;
use AppBundle\Annotation as VIA;
use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Validator\Constraints as VIC;
use AppBundle\Validator\Constraints\MobilePhone;

class RegistrCommand extends Message
{
    /**
     * @Assert\NotBlank(message="Введете Вашу фамилию")
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
     * @VIC\Enum("AppBundle\Enum\PersonGender")
     */
    public $gender;

    /**
     * @Assert\Date
     */
    public $birthday;

    /**
     * @Assert\NotBlank(message="Укажите город")
     * @Assert\Type(type="string")
     */
    public $geoCityName;

    /**
     * @Assert\Type(type="integer")
     */
    public $geoCityId;

    /**
     * @Assert\Type(type="string")
     * @MobilePhone
     */
    public $mobile;

    /**
     * @VIA\Description("Дополнительные телефонные номера")
     * @Assert\Type(type="array")
     */
    public $phones;

    /**
     * @Assert\NotBlank(message="Введите Ваш emal")
     * @Assert\Type(type="string")
     * @Assert\Email(message="Неверный формат email")
     */
    public $email;

    /**
     * @Assert\NotBlank(message="Придумайте пароль")
     * @Assert\Type(type="string")
     */
    public $password;

    /**
     * @Assert\NotBlank(message="Повторите пароль")
     * @Assert\Type(type="string")
     */
    public $passwordConfirm;

    /**
     * @VIA\Description("Уведомлять о сезонных распродажах")
     * @Assert\Type(type="boolean")
     */
    public $isMarketingSubscribed;

    /**
     * @Assert\NotBlank(message="Отметьте флажок если вы человек")
     * @Assert\Type(type="boolean")
     */
    public $isHuman;

    public function setGeoCityId($geoCityId)
    {
        $this->geoCityId = (int) $geoCityId ?: null;
    }
}
