<?php 

namespace ShopBundle\Bus\User\Command;

use AppBundle\Bus\Message\Message;
use AppBundle\Annotation as VIA;
use Symfony\Component\Validator\Constraints as Assert;

class EditCommand extends Message
{
    /**
     * @Assert\NotBlank
     * @Assert\Type(type="string")
     */
    public $firstname;

    /**
     * @Assert\Type(type="string")
     */
    public $secondname;

    /**
     * @Assert\NotBlank
     * @Assert\Type(type="string")
     */
    public $lastname;

    /**
     * @Assert\NotBlank
     * @Assert\Type(type="string")
     */
    public $gender;

    /**
     * @Assert\NotBlank
     * @Assert\Type(type="string")
     */
    public $phone;

    /**
     * @Assert\Type(type="string")
     */
    public $mobile;

    /**
     * @Assert\NotBlank
     * @Assert\Type(type="string")
     * @Assert\Email
     */
    public $email;

    /**
     * @Assert\Type(type="boolean")
     */
    public $isMarketingSubscribed;

    /**
     * @Assert\NotBlank
     * @Assert\Type(type="integer")
     */
    public $geoCityId;

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