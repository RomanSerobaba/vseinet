<?php

namespace OrgBundle\Bus\Employee\Query\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class Relative
{
    /**
     * @Assert\Type(type="integer")
     */
    public $id;

    /**
     * @Assert\Type(type="integer")
     */
    public $employeeId;

    /**
     * @Assert\Type(type="string")
     */
    public $relation;

    /**
     * @Assert\Type(type="integer")
     */
    public $personId;

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
     * @Assert\Choice({"male", "female"}, strict=true)
     */
    public $gender;

    /**
     * @Assert\Date
     */
    public $birthday;

    /**
     * @Assert\Type(type="integer")
     */
    public $addressId;

    /**
     * @Assert\Type(type="AppBundle\Bus\User\Query\DTO\Address")
     */
    public $address;

    /**
     * @Assert\Type(type="integer")
     */
    public $mobileId;

    /**
     * @Assert\Type(type="string")
     */
    public $mobile;

    /**
     * Relative constructor.
     * @param $id
     * @param $employeeId
     * @param $relation
     * @param $personId
     * @param $lastname
     * @param $firstname
     * @param $secondname
     * @param $gender
     * @param $birthday
     * @param $addressId
     * @param $address
     * @param $mobile
     * @param $mobileId
     */
    public function __construct(
        $id,
        $employeeId,
        $relation,
        $personId,
        $lastname,
        $firstname,
        $secondname,
        $gender,
        $birthday,
        $addressId=null,
        $address=null,
        $mobile=null,
        $mobileId=null
    )
    {
        $this->id = $id;
        $this->employeeId = $employeeId;
        $this->relation = $relation;
        $this->personId = $personId;
        $this->lastname = $lastname;
        $this->firstname = $firstname;
        $this->secondname = $secondname;
        $this->gender = $gender;
        $this->birthday = $birthday;
        $this->addressId = $addressId;
        $this->address = $address;
        $this->mobile = $mobile;
        $this->mobileId = $mobileId;
    }
}