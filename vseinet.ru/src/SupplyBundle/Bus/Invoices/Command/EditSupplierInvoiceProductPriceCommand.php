<?php 

namespace SupplyBundle\Bus\Invoices\Command;

use AppBundle\Bus\Message\Message;
use AppBundle\Annotation as VIA;
use Symfony\Component\Validator\Constraints as Assert;

class EditSupplierInvoiceProductPriceCommand extends Message
{
    /**
     * @VIA\Description("Supplier invoice id")
     * @Assert\NotBlank
     * @Assert\Type(type="integer")
     */
    public $id;

    /**
     * @VIA\Description("Order item id")
     * @Assert\NotBlank
     * @Assert\Type(type="integer")
     */
    public $orderItemId;

    /**
     * @VIA\Description("Purchase price")
     * @Assert\NotBlank
     * @Assert\Type(type="float")
     */
    public $purchasePrice;

    /**
     * @VIA\Description("New purchase price")
     * @Assert\NotBlank
     * @Assert\Type(type="float")
     */
    public $newPurchasePrice;

    /**
     * @VIA\Description("Supplier reserve id")
     * @Assert\Type(type="integer")
     */
    public $supplierReserveId;
}