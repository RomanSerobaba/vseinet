<?php 

namespace AppBundle\Entity;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * UserData
 */
class UserData
{
    /**
     * @Assert\Type(type="boolean")
     */
    public $isFired;

    /**
     * @Assert\Type(type="datetime")
     */
    public $clockInTime;

    /**
     * @Assert\Type(type="string")
     */
    public $ipAddress;
}
