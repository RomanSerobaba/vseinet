<?php 

namespace ContentBundle\Bus\Category\Command;

use AppBundle\Bus\Message\Message;
use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Annotation as VIA;

class CreateCommand extends Message
{
    /**
     * @Assert\NotBlank(message="Значение pid не должно быть пустым")
     * @Assert\Type(type="integer")
     */
    public $pid;

    /**
     * @Assert\NotBlank(message="Значение name не должно быть пустым")
     * @Assert\Type(type="string")
     */
    public $name;

    /**
     * @Assert\Type(type="integer")
     */
    public $aliasForId;

    /**
     * @Assert\Type(type="string")
     */
    public $basename;

    /**
     * @Assert\Choice({"male", "female", "neuter", "plural"}, strict=true)
     * @VIA\DefaultValue("male")
     */
    public $gender;

    /**
     * @Assert\Uuid
     */
    public $uuid;
}