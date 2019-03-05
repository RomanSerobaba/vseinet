<?php

namespace AppBundle\Bus\User\Query\DTO;

use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Validator\Constraints\Enum;
use AppBundle\Enum\PersonGender;

class UserInfo
{
    /**
     * @Assert\Type(type="integer")
     */
    public $id;

    /**
     * @Assert\Type(type="string")
     */
    public $lastname;

    /**
     * @Assert\Type(type="string")
     */
    public $firstname;

    /**
     * @Assert\Type(type="string")
     */
    public $secondname;

    /**
     * @Enum("AppBunlde\Enum\PersonGender")
     */
    public $gender;

    /**
     * @Assert\Type(type="datetime")
     */
    public $birthday;

    /**
     * @Assert\Type(type="integer")
     */
    public $geoCityId;

    /**
     * @Assert\Type(type=string)
     */
    public $geoCityName;

    /**
     * @Assert\Type(type="boolean")
     */
    public $isMarketingSubscribed;

    /**
     * @Assert\Type(type="string")
     */
    public $genderName;

    public function __construct($id, $lastname, $firstname, $secondname, $gender, $birthday, $geoCityId, $geoCityName, $isMarketingSubscribed)
    {
        $this->id = $id;
        $this->lastname = $lastname;
        $this->firstname = $firstname;
        $this->secondname = $secondname;
        $this->gender = $gender;
        $this->birthday = $birthday;
        $this->geoCityId = $geoCityId;
        $this->geoCityName = $geoCityName;
        $this->isMarketingSubscribed = $isMarketingSubscribed;
        $this->genderName = $gender ? PersonGender::getName($gender) : '';
    }
}
