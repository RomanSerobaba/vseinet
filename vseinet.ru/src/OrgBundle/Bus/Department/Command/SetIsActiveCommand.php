<?php 

namespace OrgBundle\Bus\Department\Command;

use AppBundle\Bus\Message\Message;
use AppBundle\Annotation as VIA;
use Symfony\Component\Validator\Constraints as Assert;

class SetIsActiveCommand extends Message
{
    /**
     * @VIA\Description("Department id")
     * @Assert\Type(type="integer")
     * @Assert\NotBlank(
     *     message="Department id should not be blank."
     * )
     */
    public $id;

    /**
     * @VIA\Description("Is active status")
     * @Assert\Type(type="boolean")
     */
    public $value;
}
