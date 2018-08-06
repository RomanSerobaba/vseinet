<?php 

namespace AppBundle\Bus\User\Query\DTO;

use AppBundle\Bus\Message\Message;
use Symfony\Component\Validator\Constraints as Assert;

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


    public function __construct($id, $lastname, $firstname, $secondname, $birthday, $cityId, $cityName)
    {
        $this->id = $id;
        $this->lastname = $lastname;
        $this->firstname = $firstname;
        $this->secondname = $secondname;
        $this->birthday = $birthday;
        $this->cityId = $cityId;
        $this->cityName = $cityName;
    }
}
