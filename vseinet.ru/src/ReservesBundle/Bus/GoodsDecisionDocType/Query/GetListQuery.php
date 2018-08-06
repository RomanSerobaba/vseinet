<?php 
namespace ReservesBundle\Bus\GoodsDecisionDocType\Query;

use AppBundle\Bus\Message\Message;
use AppBundle\Annotation as VIA;
use Symfony\Component\Validator\Constraints as Assert;

class GetListQuery extends Message 
{
    /**
     * @VIA\Description("Показать с неактивными типамы")
     * @Assert\Type(type="boolean")
     */
    public $withInActive;

    /**
     * @VIA\Description("Показать типы решений, подходящиие для претензии")
     * @Assert\Type(type="integer")
     */
    public $goodsIssueDocId;
    
}