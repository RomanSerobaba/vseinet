<?php 

namespace OrderBundle\Bus\Data\Command;

use AppBundle\Bus\Message\Message;
use AppBundle\Annotation as VIA;
use Symfony\Component\Validator\Constraints as Assert;

class CreateResupplyOrderFromInvoiceCommand extends Message
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
     * @VIA\Description("Purchase price")
     * @Assert\NotBlank
     * @Assert\Type(type="float")
     */
    public $purchasePrice;

    /**
     * @Assert\Uuid
     */
    public $uuid;
}