<?php 

namespace SupplyBundle\Bus\Data\Command;

use AppBundle\Bus\Message\Message;
use AppBundle\Annotation as VIA;
use Symfony\Component\Validator\Constraints as Assert;

class CreateSupplierInvoiceCommand extends Message
{
    /**
     * @VIA\Description("Supplier id")
     * @Assert\NotBlank
     * @Assert\Type(type="integer")
     */
    public $supplierId;

    /**
     * @VIA\Description("Counteragent id")
     * @Assert\NotBlank
     * @Assert\Type(type="integer")
     */
    public $counteragentId;

    /**
     * @VIA\Description("Point id")
     * @Assert\NotBlank
     * @Assert\Type(type="integer")
     */
    public $pointId;

    /**
     * @Assert\Uuid
     */
    public $uuid;
}