<?php

namespace AppBundle\Bus\User\Query\DTO;

use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Validator\Constraints\MobilePhone;
use AppBundle\Validator\Constraints\PersonName;

class UserData
{
    /**
     * @assert\Type(type="integer")
     */
    public $userId;

    /**
     * @Assert\Type(type="integer")
     */
    public $comuserId;

    /**
     * @Assert\Type(type="string")
     */
    public $position;

    /**
     * @Assert\Type(type="string")
     * @PersonName
     */
    public $fullname;

    /**
     * @Assert\All({
     *     @Assert\Type(type="AppBundle\Entity\Contact")
     * })
     */
    public $phoneList;

    /**
     * @Assert\Type(type="string")
     * @MobilePhone
     */
    public $phone;

    /**
     * @Assert\Type(type="string")
     */
    public $additionalPhone;

    /**
     * @Assert\All({
     *     @Assert\Type(type="AppBundle\Entity\Contact")
     * })
     */
    public $emailList;

    /**
     * @Assert\Type(type="string")
     * @Assert\Email
     */
    public $email;

    /**
     * @Assert\All({
     *     @Assert\Type(type="integer")
     * })
     */
    public $contactIds;

}
