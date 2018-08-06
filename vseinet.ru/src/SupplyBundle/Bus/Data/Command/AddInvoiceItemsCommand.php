<?php 

namespace SupplyBundle\Bus\Data\Command;

use AppBundle\Bus\Message\Message;
use AppBundle\Annotation as VIA;
use Symfony\Component\Validator\Constraints as Assert;

class AddInvoiceItemsCommand extends Message
{
    /**
     * @VIA\Description("Supply invoice id")
     * @Assert\NotBlank
     * @Assert\Type(type="integer")
     */
    public $id;

    /**
     * @VIA\Description("Items [{orderItemId:1, purchasePrice:2, supplierReserveId:3}, ...]")
     * @Assert\Type(type="array")
     */
    public $items;
}