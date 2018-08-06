<?php 

namespace ReservesBundle\Bus\GoodsIssueDoc\Query;

use AppBundle\Bus\Message\Message;
use AppBundle\Annotation as VIA;
use Symfony\Component\Validator\Constraints as Assert;

class ListRelatedElementsQuery extends Message 
{
    /**
     * @VIA\Description("Уникальный идентификатор претензии")
     * @Assert\Type(type="integer")
     */
    public $id;
    
}