<?php

namespace AppBundle\Bus\User\Query\DTO;

use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Validator\Constraints\MobilePhone;
use AppBundle\Validator\Constraints\PersonName;
use AppBundle\Bus\Message\Message;

class UserData extends Message
{
    /**
     * @assert\Type(type="integer", message="Идентификатор пользователя должен быть числом")
     */
    public $userId;

    /**
     * @Assert\Type(type="integer", message="Идентификатор незарегистрированного пользователя должен быть числом")
     */
    public $comuserId;

    /**
     * @Assert\NotBlank(message="Укажите Ваше ФИО")
     * @Assert\Type(type="string")
     * @PersonName
     */
    public $fullname;

    /**
     * @Assert\NotBlank(message="Укажите Ваш телефон")
     * @Assert\Type(type="string")
     * @MobilePhone
     */
    public $phone;

    /**
     * @Assert\Type(type="string")
     */
    public $additionalPhone;

    /**
     * @Assert\Type(type="string")
     * @Assert\Email
     */
    public $email;
}
