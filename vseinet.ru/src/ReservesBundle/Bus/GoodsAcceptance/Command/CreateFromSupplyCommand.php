<?php 

namespace ReservesBundle\Bus\GoodsAcceptance\Command;

use AppBundle\Bus\Message\Message;
use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Annotation as VIA;

class CreateFromSupplyCommand extends Message
{    

    /**
     * @VIA\Description("Человекочитаемый заголовок")
     * @Assert\Type(type="string")
     */
    public $title;
    
    ////////////////////////////////////////////////////////////////////////
    
    /**
     * @VIA\Description("Склад - приемник")
     * @Assert\NotBlank(message="Конечное место хранения товаров должно быть указано")
     * @Assert\Type(type="integer")
     */
    public $geoRoomId;

    /**
     * @VIA\Description("Уникальные идентификаторы заказов поставщику")
     * @Assert\Type(type="array")
     * @Assert\All(@Assert\Type(type="integer"))
     */
    public $supplyiesDocumentsIds;
    
    /**
     * @VIA\Description("Номера заказов поставщику")
     * @Assert\Type(type="array")
     * @Assert\All(@Assert\Type(type="integer"))
     */
    public $suppliesDocumentsNumbers;
    
    ////////////////////////////////////////////////////////////////////////

    /**
     * @Assert\Uuid
     */
    public $uuid;
    
}