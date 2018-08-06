<?php 

namespace SupplyBundle\Bus\Invoices\Command;

use AppBundle\Bus\Message\Message;
use AppBundle\Annotation as VIA;
use Symfony\Component\Validator\Constraints as Assert;

class CreateSeparateSupplierInvoiceCommand extends Message
{
    /**
     * @VIA\Description("Supplier invoice id")
     * @Assert\NotBlank
     * @Assert\Type(type="integer")
     */
    public $id;

    /**
     * @VIA\Description("Geo point id")
     * @Assert\NotBlank
     * @Assert\Type(type="integer")
     */
    public $geoPointId;

    /**
     * @Assert\Uuid
     */
    public $uuid;
}