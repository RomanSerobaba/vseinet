<?php

namespace AppBundle\Bus\Order\Command\Schema;

use AppBundle\Bus\Message\Message;
use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Validator\Constraints\MobilePhone;
use AppBundle\Validator\Constraints\PersonName;

class Client extends Message
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

    public function setUserId($userId)
    {
        $this->userId = empty($userId) ? null : (int) $userId;
    }

    public function setComuserId($comuserId)
    {
        $this->comuserId = empty($comuserId) ? null : (int) $comuserId;
    }

    public function setPhone($phone)
    {
        $this->phone = empty($phone) ? null : $phone;
    }

    public function setAdditionalPhone($additionalPhone)
    {
        $this->additionalPhone = empty($additionalPhone) ? null : $additionalPhone;
    }

    public function setEmail($email)
    {
        $this->email = empty($email) ? null : $email;
    }
}
