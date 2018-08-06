<?php 

namespace ShopBundle\Bus\User\Query\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class Account
{
    /**
     * @Assert\Type(type="integer")
     */
    public $id;

    /**
     * @Assert\Type(type="integer")
     */
    public $userId;

    /**
     * @Assert\Type(type="string")
     */
    public $firstname;

    /**
     * @Assert\Type(type="string")
     */
    public $secondname;

    /**
     * @Assert\Type(type="string")
     */
    public $lastname;

    /**
     * @Assert\Type(type="string")
     */
    public $fullname;

    /**
     * @Assert\Type(type="string")
     */
    public $gender;

    /**
     * @Assert\Type(type="date")
     */
    public $birthday;

    /**
     * @Assert\Type(type="string")
     */
    public $phone;

    /**
     * @Assert\Type(type="string")
     */
    public $mobile;

    /**
     * @Assert\Type(type="string")
     */
    public $email;

    /**
     * @Assert\Type(type="boolean")
     */
    public $isMarketingSubscribed;

    /**
     * @Assert\Type(type="boolean")
     */
    public $isTransactionalSubscribed;

    /**
     * @Assert\Type(type="integer")
     */
    public $geoCityId;

    /**
     * @Assert\Type(type="integer")
     */
    public $geoCityName;

    /**
     * @Assert\Type(type="string")
     */
    public $street;

    /**
     * @Assert\Type(type="string")
     */
    public $home;

    /**
     * @Assert\Type(type="string")
     */
    public $housing;

    /**
     * @Assert\Type(type="string")
     */
    public $apartment;

    /**
     * @Assert\Type(type="string")
     */
    public $floor;

    /**
     * @Assert\Type(type="boolean")
     */
    public $hasLift;
}