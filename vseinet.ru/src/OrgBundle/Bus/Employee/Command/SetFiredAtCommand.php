<?php 

namespace OrgBundle\Bus\Employee\Command;

use AppBundle\Bus\Message\Message;
use AppBundle\Annotation as VIA;
use Symfony\Component\Validator\Constraints as Assert;

class SetFiredAtCommand extends Message
{
    /**
     * @VIA\Description("Employee id")
     * @Assert\Type(type="integer")
     * @Assert\NotBlank(
     *     message="Employee id should not be blank."
     * )
     */
    public $id;

    /**
     * @VIA\Description("Fired at")
     * @Assert\Date
     * @Assert\NotBlank(
     *     message="Fired at should not be blank."
     * )
     */
    public $value;
}
