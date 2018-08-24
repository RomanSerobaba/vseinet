<?php 

namespace AppBundle\Bus\User\Command;

use AppBundle\Bus\Message\Message;
use Symfony\Component\Validator\Constraints as Assert;

class IdentifyCommand extends Message
{
    /**
     * @Assert\Type(type="AppBundle\Bus\User\Query\DTO\UserData")
     */
    public $userData;

    // /**
    //  * @assert\Type(type="integer")
    //  */
    // public $userId;

    // /**
    //  * @Assert\Type(type="integer")
    //  */
    // public $comuserId;

    // /**
    //  * @Assert\NotBlank
    //  * @Assert\Type(type="string")
    //  */
    // public $fullname;

    // *
    //  * @Assert\NotBlank
    //  * @Assert\Type(type="string")
     
    // public $phone;

    // /**
    //  * @Assert\Type(type="string")
    //  */
    // public $additionalPhone;

    // /**
    //  * @Assert\Type(type="string")
    //  */
    // public $email;

    // /**
    //  * @Assert\Type(type="array<integer>")
    //  */
    // public $contactIds;
}
