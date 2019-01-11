<?php

namespace AppBundle\Bus\User\Query\DTO;

use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Service\PhoneFormatter;

class FoundUser
{
    /**
     * @Assert\Type(type="integer")
     */
    public $id;

    /**
     * @Assert\Type(type="string")
     */
    public $fullname;

    /**
     * @Assert\Type(type="string")
     */
    public $phone;

    /**
     * @Assert\Type(type="string")
     */
    public $formattedPhone;

    /**
     * @Assert\Type(type="string")
     */
    public $additionalPhone;

    /**
     * @Assert\Type(type="string")
     */
    public $email;

    /**
     * @Assert\Choice({"user", "comuser"}, strict=true)
     */
    public $type;

    /**
     * @Assert\Type(type="boolean")
     */
    public $isEmployee;


    public function __construct($id, $fullname, $phone, $email, $additionalPhone, $type, $isEmployee = false)
    {
        $this->id = $id;
        $this->fullname = $fullname;
        $this->phone = $phone ?? '';

        if (!empty($phone)) {
            $formatter = new PhoneFormatter();
            $this->formattedPhone = $formatter->format($phone) ?? $phone;
        } else {
            $this->formattedPhone = '';
        }

        $this->additionalPhone = $additionalPhone ?? '';
        $this->email = $email ?? '';
        $this->type = $type;
        $this->isEmployee = (bool) $isEmployee;
    }
}
