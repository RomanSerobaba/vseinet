<?php 

namespace OrderBundle\Bus\Reserves\Query;

use AppBundle\Bus\Message\Message;
use AppBundle\Annotation as VIA;
use Symfony\Component\Validator\Constraints as Assert;

class GetReserveConfirmationQuery extends Message
{
    /**
     * @VIA\Description("Order item id")
     * @Assert\NotBlank
     * @Assert\Type(type="integer")
     */
    public $id;

    /**
     * [['geo_point_id' => 1, 'quantity' => 7, 'is_in_transit' => false], ...]
     *
     * @VIA\Description("Quantities")
     * @Assert\NotBlank
     * @Assert\Type(type="array")
     */
    public $quantities;

    /**
     * @VIA\Description("Supplier reserve id")
     * @Assert\Type(type="integer")
     */
    public $supplierReserveId;
}