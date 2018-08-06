<?php 

namespace SupplyBundle\Bus\Invoices\Command;

use AppBundle\Bus\Message\Message;
use AppBundle\Annotation as VIA;
use Symfony\Component\Validator\Constraints as Assert;

class EditSupplierInvoiceItemPriceCommand extends Message
{
    /**
     * @VIA\Description("Supplier invoice id")
     * @Assert\NotBlank
     * @Assert\Type(type="integer")
     */
    public $id;

    /**
     * @VIA\Description("Base product id")
     * @Assert\NotBlank
     * @Assert\Type(type="integer")
     */
    public $baseProductId;

    /**
     * @VIA\Description("New purchase price")
     * @Assert\NotBlank
     * @Assert\Type(type="float")
     */
    public $newPurchasePrice;
}