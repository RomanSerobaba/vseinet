<?php

namespace AppBundle\Bus\Security\Command;

use AppBundle\Bus\Message\Message;
use AppBundle\Annotation as VIA;
use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Validator\Constraints as VIC;
use AppBundle\Validator\Constraints\MobilePhone;
use Symfony\Component\Validator\Context\ExecutionContext;

class RegistrCommand extends Message
{
    /**
     * @Assert\NotBlank(message="Введете Вашу фамилию")
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
     * @VIC\Enum("AppBundle\Enum\PersonGender")
     */
    public $gender;

    /**
     * @Assert\Date
     */
    public $birthday;

    /**
     * @Assert\NotBlank(message="Укажите город")
     * @Assert\Type("string")
     */
    public $geoCityName;

    /**
     * @Assert\Type("integer")
     */
    public $geoCityId;

    /**
     * @Assert\Type("string")
     * @MobilePhone
     */
    public $mobile;

    /**
     * @VIA\Description("Дополнительные телефонные номера")
     * @Assert\Type("array")
     */
    public $phones;

    /**
     * @Assert\NotBlank(message="Введите Ваш emal")
     * @Assert\Type("string")
     * @Assert\Email(message="Неверный формат email")
     */
    public $email;

    /**
     * @Assert\NotBlank(message="Придумайте пароль")
     * @Assert\Type("string")
     */
    public $password;

    /**
     * @Assert\NotBlank(message="Повторите пароль")
     * @Assert\Type("string")
     */
    public $passwordConfirm;

    /**
     * @VIA\Description("Уведомлять о сезонных распродажах")
     * @Assert\Type("boolean")
     */
    public $isMarketingSubscribed;

    /**
     * @Assert\Callback
     */
    public function validate(ExecutionContext $context, $payload)
    {
        if (empty($this->geoCityId)) {
            $context->buildViolation('Необходимо указать город')
                ->atPath('geoCityName')
                ->addViolation();
        }
    }
}
