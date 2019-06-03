<?php

namespace AppBundle\Bus\User\Query\DTO;

use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Validator\Constraints\MobilePhone;
use AppBundle\Validator\Constraints\PersonName;
use AppBundle\Bus\Message\Message;

class UserData extends Message
{
    /**
     * @assert\Type("integer")
     */
    public $userId;

    /**
     * @Assert\Type("integer")
     */
    public $comuserId;

    /**
     * @Assert\NotBlank(message="Укажите Ваше ФИО")
     * @Assert\Type("string")
     * @PersonName
     */
    public $fullname;

    /**
     * @Assert\NotBlank(message="Укажите Ваш телефон")
     * @Assert\Type("string")
     * @MobilePhone
     */
    public $phone;

    /**
     * @Assert\Type("string")
     */
    public $additionalPhone;

    /**
     * @Assert\Type(type="string")
     * @Assert\Email(message="Неверный формат email")
     */
    public $email;

    /**
     * @Assert\All(@Assert\Type("integer"))
     */
    public $contactIds = [];
}
