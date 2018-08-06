<?php 

namespace SupplyBundle\Bus\Suppliers\Command;

use AppBundle\Bus\Message\Message;
use AppBundle\Annotation as VIA;
use Symfony\Component\Validator\Constraints as Assert;

class SetMainCounteragentCommand extends Message
{
    /**
     * @VIA\Description("Supplier id")
     * @Assert\NotBlank
     * @Assert\Type(type="integer")
     */
    public $id;

    /**
     * @VIA\Description("Сounteragent id")
     * @Assert\NotBlank
     * @Assert\Type(type="integer")
     */
    public $counteragentId;
}