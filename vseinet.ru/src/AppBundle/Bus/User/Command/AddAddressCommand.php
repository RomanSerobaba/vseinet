<?php

namespace AppBundle\Bus\User\Command;

use AppBundle\Bus\Message\Message;
use Symfony\Component\Validator\Constraints as Assert;

class AddAddressCommand extends Message
{
    /**
     * @Assert\Type("integer")
     */
    public $id;

    /**
     * @Assert\NotBlank(message="Выберите город")
     * @Assert\Type("string")
     */
    public $geoCityName;

    /**
     * @Assert\Type("integer")
     */
    public $geoCityId;

    /**
     * @Assert\NotBlank(message="Выберите улицу")
     * @Assert\Type("string")
     */
    public $geoStreetName;

    /**
     * @Assert\Type("integer")
     */
    public $geoStreetId;

    /**
     * @Assert\NotBlank(message="Введите номер дома")
     * @Assert\Type("string")
     */
    public $house;

    /**
     * @Assert\Type("string")
     */
    public $building;

    /**
     * @Assert\Type("string")
     */
    public $apartment;

    /**
     * @Assert\Type("integer", message="Этаж - целое положительное число")
     * @Assert\GreaterThan(value=0, message="Этаж - целое положительное число")
     */
    public $floor;

    /**
     * @Assert\Type("boolean")
     */
    public $hasLift;

    /**
     * @Assert\Type("string")
     */
    public $comment;

    /**
     * @Assert\Type("boolean")
     */
    public $isMain;
}
