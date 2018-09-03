<?php 

namespace AppBundle\Bus\User\Query\DTO;

use AppBundle\Bus\Message\Message;
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
    public $cityId;

    /**
     * @Assert\Type(type='string')
     */
    public $cityName;

    /**
     * @Assert\Type(type="boolean")
     */
    public $isMarketingSubscribed;

    /**
     * @Assert\Type(type="string")
     */
    public $genderName;


    public function __construct($id, $lastname, $firstname, $secondname, $gender, $birthday, $cityId, $cityName, $isMarketingSubscribed)
    {
        $this->id = $id;
        $this->lastname = $lastname;
        $this->firstname = $firstname;
        $this->secondname = $secondname;
        $this->gender = $gender;
        $this->birthday = $birthday;
        $this->cityId = $cityId;
        $this->cityName = $cityName;
        $this->isMarketingSubscribed = $isMarketingSubscribed;
        $this->genderName = PersonGender::getName($gender);
    }
}
