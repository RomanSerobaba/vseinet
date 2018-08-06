<?php 

namespace ContentBundle\Bus\Detail\Command;

use AppBundle\Bus\Message\Message;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @internal
 */
class CreateDependCommand extends Message 
{
    /**
     * @Assert\NotBlank(message="Значение id не может быть пустым")
     * @Assert\Type(type="integer")
     */
    public $id;

    /**
     * @Assert\NotBlank(message="Значение groupId не может быть пустым")
     * @Assert\Type(type="integer")
     */
    public $groupId;

    /**
     * @Assert\Type(type="integer")
     */
    public $sectionId;

    /**
     * @Assert\NotBlank(message="Значение typeCode не может быть пустым")
     * @Assert\Choice({"size", "range", "dimensions"}, strict=true)
     */
    public $typeCode;

    /**
     * @Assert\Type(type="integer")
     */
    public $unitId;
}