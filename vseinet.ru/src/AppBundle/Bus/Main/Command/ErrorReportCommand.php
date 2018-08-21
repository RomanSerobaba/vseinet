<?php 

namespace AppBundle\Bus\Main\Command;

use AppBundle\Bus\Message\Message;
use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Validator\Constraints\Enum;

class ErrorReportCommand extends Message
{
    /**
     * @Assert\NotBlank
     * @Assert\Type(type="string")
     */
    public $url;

    /**
     * @Enum("AppBundle\Enum\ErrorReportNode")
     */
    public $node;

    /**
     * @Assert\NotBlank
     * @Assert\Type(type="string")
     */
    public $text;
}
