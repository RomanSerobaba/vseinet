<?php 

namespace ReservesBundle\Bus\GoodsPackagingItem\Query;

use AppBundle\Bus\Message\Message;
use AppBundle\Annotation as VIA;
use Symfony\Component\Validator\Constraints as Assert;

class GetItemQuery extends Message 
{
    /**
     * @VIA\Description("Идентификатор документа комплектации/разкомплектации")
     * @Assert\Type(type="integer")
     */
    public $goodsPackagingId;

    /**
     * @VIA\Description("Идентификатор товара")
     * @Assert\Type(type="integer")
     */
    public $baseProductId;
}