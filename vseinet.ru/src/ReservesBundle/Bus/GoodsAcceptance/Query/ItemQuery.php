<?php 

namespace ReservesBundle\Bus\GoodsAcceptance\Query;

use AppBundle\Bus\Message\Message;
use AppBundle\Annotation as VIA;
use Symfony\Component\Validator\Constraints as Assert;

class ItemQuery extends Message 
{
    /**
     * @VIA\Description("Уникальный идентификатор документа")
     * @Assert\Type(type="integer")
     */
    public $id;
}