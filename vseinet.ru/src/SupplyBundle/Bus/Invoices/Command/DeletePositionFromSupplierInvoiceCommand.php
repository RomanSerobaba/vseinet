<?php 

namespace SupplyBundle\Bus\Invoices\Command;

use AppBundle\Bus\Message\Message;
use AppBundle\Annotation as VIA;
use Symfony\Component\Validator\Constraints as Assert;

class DeletePositionFromSupplierInvoiceCommand extends Message
{
    /**
     * @VIA\Description("Supplier invoice id")
     * @Assert\NotBlank
     * @Assert\Type(type="integer")
     */
    public $id;

    /**
     * @VIA\Description("Items [{order_item_id: orderItemId, purchase_price: purchasePrice, supplier_reserve_id: supplierReserveId}}, ...]")
     * @Assert\NotBlank
     * @Assert\Type(type="array")
     */
    public $items;
}