<?php 

namespace OrderBundle\Bus\Data\Command;

use AppBundle\Bus\Message\Message;
use AppBundle\Annotation as VIA;
use Symfony\Component\Validator\Constraints as Assert;

class CreateResupplyOrderFromLowCostPurchasesCommand extends Message
{
    /**
     * @VIA\Description("Type code")
     * @Assert\NotBlank
     * @Assert\Type(type="string")
     * @VIA\DefaultValue("resupply")
     */
    public $typeCode;

    /**
     * @VIA\Description("Items [{'baseProductId' => 1, 'quantity' => 2}, ...]")
     * @Assert\NotBlank
     * @Assert\Type(type="array")
     */
    public $items;

    /**
     * @Assert\Uuid
     */
    public $uuid;
}