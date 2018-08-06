<?php

namespace OrderBundle\Bus\Data\Command;

use AppBundle\Bus\Message\Message;
use AppBundle\Annotation as VIA;
use Symfony\Component\Validator\Constraints as Assert;

class AddCommentCommand extends Message
{
    /**
     * @VIA\Description("Order id")
     * @Assert\NotBlank
     * @Assert\Type(type="integer")
     */
    public $id;

    /**
     * @VIA\Description("Comment")
     * @Assert\NotBlank
     * @Assert\Type(type="string")
     */
    public $text;

    /**
     * @VIA\Description("Is important")
     * @Assert\Type(type="boolean")
     * @VIA\DefaultValue(false)
     */
    public $isImportant;

    /**
     * @Assert\Uuid
     */
    public $uuid;
}