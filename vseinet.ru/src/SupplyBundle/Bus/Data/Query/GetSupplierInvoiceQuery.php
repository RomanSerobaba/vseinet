<?php 

namespace SupplyBundle\Bus\Data\Query;

use AppBundle\Bus\Message\Message;
use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Annotation as VIA;

class GetSupplierInvoiceQuery extends Message
{
    /**
     * @VIA\Description("Supplier invoice id")
     * @Assert\NotBlank
     * @Assert\Type(type="integer")
     */
    public $id;
}