<?php 

namespace OrgBundle\Bus\Representative\Command;

use AppBundle\Bus\Message\Message;
use AppBundle\Annotation as VIA;
use Symfony\Component\Validator\Constraints as Assert;

class SetIsCentralCommand extends Message
{
    /**
     * @VIA\Description("Representative id")
     * @Assert\NotBlank
     * @Assert\Type(type="integer")
     */
    public $id;

    /**
     * @VIA\Description("Центральная, Главная точка")
     * @Assert\Type(type="boolean")
     */
    public $isCentral;
}