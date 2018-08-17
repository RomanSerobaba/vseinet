<?php 

namespace AppBundle\Bus\Complaint\Command;

use AppBundle\Bus\Message\Message;
use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Validator\Constraints\Phone;

class HandleCommand extends Message
{
    /**
     * @Assert\NotBlank(message="Выберите причину")
     * @Assert\Choice({})
     */
    public $type;

    /**
     * @Assert\Type(type="integer")
     */
    public $managerId;

    /**
     * @Assert\Type(type="string")
     * @Phone()
     */
    public $managerPhone;

    /**
     * @Assert\NotBlank(message="Опишите причину в сообщении")
     * @Assert\Type(type="string")
     */
    public $text;

    /**
     * @Assert\Type(type="string")
     */
    public $firstname;

    /**
     * @Phone()
     */
    public $phone;

    /**
     * @Assert\Email()
     */
    public $email;

    /**
     * @Assert\Type(type="boolean")
     */
    public $isHuman;
}