<?php 

namespace SupplyBundle\Bus\Reserves\Query;

use AppBundle\Bus\Message\Message;
use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Annotation as VIA;

class GetIndexQuery extends Message
{
    /**
     * @Assert\Type(type="integer")
     * @Assert\NotBlank
     * @VIA\Description("Supplier reserve id")
     */
    public $id;

    /**
     * @Assert\Type(type="integer")
     * @VIA\Description("Точка")
     * @VIA\DefaultValue("0")
     */
    public $pointId;

    /**
     * @Assert\Type(type="boolean")
     * @VIA\Description("Вместе с зарезервированными")
     * @VIA\DefaultValue("false")
     */
    public $withConfirmedReserves;
}