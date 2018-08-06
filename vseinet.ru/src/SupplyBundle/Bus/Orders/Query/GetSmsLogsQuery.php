<?php 

namespace SupplyBundle\Bus\Orders\Query;

use AppBundle\Bus\Message\Message;
use AppBundle\Annotation as VIA;
use Symfony\Component\Validator\Constraints as Assert;

class GetSmsLogsQuery extends Message
{
    /**
     * @Assert\Type(type="integer")
     * @VIA\Description("Заказ")
     */
    public $id;
}