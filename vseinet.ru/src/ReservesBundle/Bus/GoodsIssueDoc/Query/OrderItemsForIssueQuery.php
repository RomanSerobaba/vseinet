<?php 

namespace ReservesBundle\Bus\GoodsIssueDoc\Query;

use AppBundle\Bus\Message\Message;
use AppBundle\Annotation as VIA;
use Symfony\Component\Validator\Constraints as Assert;

class OrderItemsForIssueQuery extends Message 
{
    /**
     * @VIA\Description("Номер заказа")
     * @Assert\NotBlank(message="Идентификатор документа должен быть указан")
     * @Assert\Type(type="integer")
     */
    public $orderNumber;
    
}