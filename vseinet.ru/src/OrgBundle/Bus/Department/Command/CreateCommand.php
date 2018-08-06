<?php 

namespace OrgBundle\Bus\Department\Command;

use AppBundle\Bus\Message\Message;
use AppBundle\Annotation as VIA;
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
     * @VIA\Description("Name of department")
     * @Assert\Type(type="string")
     * @Assert\NotBlank(
     *     message="Name should not be blank."
     * )
     */
    public $name;

    /**
     * @VIA\Description("Department type code")
     * @Assert\Choice({"standard", "trade_area", "outlet"})
     * @Assert\NotBlank
     */
    public $departmentTypeCode;

    /**
     * @Assert\Uuid
     */
    public $uuid;
}
