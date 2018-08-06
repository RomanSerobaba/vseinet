<?php 

namespace SupplyBundle\Bus\Data\Command;

use AppBundle\Bus\Message\Message;
use AppBundle\Annotation as VIA;
use Symfony\Component\Validator\Constraints as Assert;

class ChangeSupplierInvoiceNumberCommand extends Message
{
    /**
     * @VIA\Description("Supplier invoice id")
     * @Assert\NotBlank
     * @Assert\Type(type="integer")
     */
    public $id;

    /**
     * @VIA\Description("Number")
     * @Assert\NotBlank
     * @Assert\Type(type="string")
     */
    public $supplierInvoiceNumber;
}