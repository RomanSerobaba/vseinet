<?php

namespace OrgBundle\Bus\Employee\Command\Schema;

use Symfony\Component\Validator\Constraints as Assert;

class Relative
{
    /**
     * @var int
     *
     * @Assert\Type(type="numeric")
     */
    public $id;

    /**
     * @var string
     *
     * @Assert\Type(type="string")
     * @Assert\NotBlank(message="Relation should not be blank")
     */
    public $relation;

    /**
     * @var int
     *
     * @Assert\Type(type="numeric")
     */
    public $personId;

    /**
     * @var string
     *
     * @Assert\Type(type="string")
     */
    public $lastname;

    /**
     * @var string
     *
     * @Assert\Type(type="string")
     */
    public $firstname;

    /**
     * @var string
     *
     * @Assert\Type(type="string")
     */
    public $secondname;

    /**
     * @var int
     *
     * @Assert\Type(type="numeric")
     */
    public $mobileId;

    /**
     * @var string
     *
     * @Assert\Type(type="string")
     * @Assert\NotBlank(message="Mobile phone should not be blank")
     */
    public $mobile;

    /**
     * @var int
     *
     * @Assert\Type(type="numeric")
     */
    public $geoStreetId;

    /**
     * @var string
     *
     * @Assert\Type(type="string")
     */
    public $house;

    /**
     * @var string
     *
     * @Assert\Type(type="string")
     */
    public $building;

    /**
     * @var string
     *
     * @Assert\Type(type="string")
     */
    public $apartment;
}