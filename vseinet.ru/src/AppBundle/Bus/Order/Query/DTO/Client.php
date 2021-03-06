<?php

namespace AppBundle\Bus\Order\Query\DTO;

use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Validator\Constraints\MobilePhone;
use AppBundle\Validator\Constraints\PersonName;

class Client
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
     * @Assert\Type(type="string")
     */
    public $position;

    /**
     * @Assert\Type(type="string")
     * @PersonName
     */
    public $fullname;

    /**
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
