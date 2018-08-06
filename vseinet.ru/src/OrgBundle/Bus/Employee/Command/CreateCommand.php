<?php 

namespace OrgBundle\Bus\Employee\Command;

use AppBundle\Annotation as VIA;
use AppBundle\Bus\Message\Message;
use Symfony\Component\Validator\Constraints as Assert;

class CreateCommand extends Message
{
    /**
     * @VIA\Description("Number")
     * @Assert\Type(type="string")
     * @Assert\NotBlank(
     *     message="Number should not be blank."
     * )
     * @Assert\Regex(
     *     pattern="/^\d+(\.\d+)*\.?$/",
     *     htmlPattern="^\d+(\.\d+)*\.?$",
     *     message="Number has invalid format"
     * )
     */
    public $number;

    /**
     * @VIA\Description("UserId")
     * @Assert\Type(type="integer")
     * @Assert\NotBlank
     */
    public $userId;

    /**
     * @Assert\Uuid
     */
    public $uuid;
}
