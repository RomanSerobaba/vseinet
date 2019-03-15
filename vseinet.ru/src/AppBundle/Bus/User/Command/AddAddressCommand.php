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
     * @Assert\NotBlank(message="Выберите город")
     * @Assert\Type(type="string")
     */
    public $geoCityName;

    /**
     * @Assert\Type(type="integer")
     */
    public $geoCityId;

    /**
     * @Assert\NotBlank(message="Выберите улицу")
     * @Assert\Type(type="string")
     */
    public $geoStreetName;

    /**
     * @Assert\Type(type="integer")
     */
    public $geoStreetId;

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

    public function setGeoCityId($geoCityId)
    {
        $this->geoCityId = (int) $geoCityId ?: null;
    }

    public function setGeoStreetId($geoStreetId)
    {
        $this->geoStreetId = (int) $geoStreetId ?: null;
    }
}
