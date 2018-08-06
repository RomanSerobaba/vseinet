<?php 

namespace ReservesBundle\Bus\GoodsPackagingItem\Query;

use AppBundle\Bus\Message\Message;
use AppBundle\Annotation as VIA;
use Symfony\Component\Validator\Constraints as Assert;

class GetQuery extends Message 
{
    /**
     * @VIA\Description("Идентификатор документа комплектации/разкомплектации")
     * @Assert\Type(type="integer")
     */
    public $goodsPackagingId;

}