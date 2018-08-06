<?php 

namespace ReservesBundle\Bus\GoodsReleaseDoc\Query;

use AppBundle\Bus\Message\Message;
use AppBundle\Annotation as VIA;
use Symfony\Component\Validator\Constraints as Assert;

class GetItemQuery extends Message 
{
    /**
     * @VIA\Description("Идентификатор документа выдачи товара покупателю")
     * @Assert\Type(type="integer")
     */
    public $id;
}