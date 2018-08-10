<?php 

namespace AppBundle\Bus\User\Command;

use AppBundle\Bus\Message\Message;
use Symfony\Component\Validator\Constraints as Assert;

class AddAddressCommand extends Message
{
    /**
     * @Assert\Type(type="integer")
     */
    public $id;

    /**
     * @Assert\NotBlank(message="Введите адрес")
     * @Assert\Type(type="string")
     */
    public $address;

    /**
     * @Assert\NotBlank(message="Введите номер дома")
     * @Assert\Type(type="string")
     */
    public $house;

    /**
     * @Assert\Type(type="string")
     */
    public $building;

    /**
     * @Assert\Type(type="string")
     */
    public $apartment;

    /**
     * @Assert\Type(type="integer", message="Этаж - целое положительное число")
     * @Assert\GreaterThan(value=0, message="Этаж - целое положительное число")
     */
    public $floor;

    /**
     * @Assert\Type(type="boolean")
     */
    public $hasLift;

    /**
     * @Assert\Type(type="string")
     */
    public $comment;

    /**
     * @Assert\Type(type="boolean")
     */
    public $isMain;

    /**
     * @Assert\Type(type="integer")
     */
    public $variant;

    /**
     * @Assert\Type(type="array")
     */
    public $variants;

    /**
     * @Assert\Type(type="string")
     */
    public $structuredAddress;
}
