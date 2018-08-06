<?php
namespace ReservesBundle\Bus\GoodsIssueDoc\Query\DTO;

use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Annotation as VIA;

/**
 * Description of DocumentStatus
 *
 * @author denis
 */
class OrderItemsForIssueDTO {
    
    /**
     * @VIA\Description("Идентификатор товара")
     * @Assert\Type(type="integer")
     */
    public $id;
    
    /**
     * @VIA\Description("Уникальный идентификатор заказа")
     * @Assert\Type(type="integer")
     */
    public $orderId;
    
    /**
     * @VIA\Description("Идентификатор позиции заказа")
     * @Assert\Type(type="integer")
     */
    public $orderItemId;
    
    /**
     * @VIA\Description("Количество")
     * @Assert\Type(type="integer")
     */
    public $quantity;
    
    /**
     * @VIA\Description("партия товарв")
     * @Assert\Type(type="integer")
     */
    public $supplyItemId;
    
    /**
     * @VIA\Description("Наименование")
     * @Assert\Type(type="string")
     */
    public $name;
    
}
