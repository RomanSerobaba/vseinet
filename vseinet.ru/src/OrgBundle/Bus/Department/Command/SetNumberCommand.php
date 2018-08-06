<?php 

namespace OrgBundle\Bus\Department\Command;

use AppBundle\Bus\Message\Message;
use AppBundle\Annotation as VIA;
use Symfony\Component\Validator\Constraints as Assert;

class SetNumberCommand extends Message
{
    /**
     * @VIA\Description("Department id")
     * @Assert\Type(type="integer")
     * @Assert\GreaterThan(0)
     * @Assert\NotBlank(
     *     message="Department id should not be blank."
     * )
     */
    public $id;

    /**
     * @VIA\Description("New value of the number")
     * @Assert\Type(type="string")
     * @Assert\NotBlank(
     *     message="Number should not be blank."
     * )
     * @Assert\Regex(
     *     pattern="/^(\d+\.)*\d+$/",
     *     htmlPattern="^\d+(\.\d+)*\.?$",
     *     message="Number has invalid format"
     * )
     */
    public $value;
}
