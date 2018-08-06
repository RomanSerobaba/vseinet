<?php 

namespace SupplyBundle\Bus\Data\Query;

use AppBundle\Bus\Message\Message;
use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Annotation as VIA;

class GetSupplierWithInvoicesQuery extends Message
{
    /**
     * @VIA\Description("Active")
     * @Assert\Type(type="string")
     * @Assert\Choice({
     *     "",
     *     "waybill",
     *     "transit",
     *     "forming"
     * }, strict=true)
     */
    public $state;

    /**
     * @VIA\Description("Supplier id")
     * @Assert\Type(type="integer")
     */
    public $supplierId;

    /**
     * @VIA\Description("From date")
     * @Assert\Type(type="datetime")
     * VIA\DefaultValue(null)
     */
    public $fromDate;

    /**
     * @VIA\Description("To date")
     * @Assert\Type(type="datetime")
     * VIA\DefaultValue(null)
     */
    public $toDate;

    /**
     * @VIA\Description("Supply id")
     * @Assert\Type(type="integer")
     */
    public $supplyId;
}