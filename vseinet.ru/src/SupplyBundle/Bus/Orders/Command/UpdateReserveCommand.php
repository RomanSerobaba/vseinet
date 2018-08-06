<?php 

namespace SupplyBundle\Bus\Orders\Command;

use AppBundle\Bus\Message\Message;
use AppBundle\Annotation as VIA;
use Symfony\Component\Validator\Constraints as Assert;

class UpdateReserveCommand extends Message
{
    /**
     * @VIA\Description("Base product id")
     * @Assert\NotBlank
     * @Assert\Type(type="integer")
     */
    public $baseProductId;

    /**
     * [geo_point_id => quantity, ...]
     *
     * @VIA\Description("Warehouse")
     * @Assert\Type(type="array")
     */
    public $warehouse;

    /**
     * [geo_point_id => quantity, ...]
     *
     * @VIA\Description("Transit")
     * @Assert\Type(type="array")
     */
    public $transit;
}