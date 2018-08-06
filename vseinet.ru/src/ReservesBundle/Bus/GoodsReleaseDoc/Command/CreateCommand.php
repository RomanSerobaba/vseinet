<?php 

namespace ReservesBundle\Bus\GoodsReleaseDoc\Command;

use AppBundle\Bus\Message\Message;
use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Annotation as VIA;

class CreateCommand extends Message
{    

    /**
     * @VIA\Description("Человекочитаемый заголовок документа")
     * @Assert\Type(type="string")
     */
    public $title;
    
    /**
     * @VIA\Description("Склад - источник")
     * @Assert\NotBlank(message="Место хранения товаров должно быть указано")
     * @Assert\Type(type="integer")
     */
    public $geoRoomId;

    /**
     * @VIA\Description("Склад - приемник")
     * @Assert\Type(type="integer")
     */
    public $destinationRoomId;

    /**
     * @VIA\Description("Глобальный идентификатор документа родителя")
     * @Assert\Type(type="integer")
     */
    public $parentDocumentId;

    /**
     * @VIA\Description("Типов документа:
     * - client   выдача клиенту
     * - transit  перемещение между подразделениями
     * - movement перемещение внутри подразделения
     * - issue    отгрузка на ремонт
     * - freight  отгрузка в транспортную компанию
     * - courier  отгрузка курьеру")
     * @Assert\Choice({"client", "transit", "movement", "issue", "freight", "courier"}, strict=true, multiple=false)
     * @Assert\NotBlank(message="Тип документа должен быть указан")
     */
    public $goodsReleaseType;
            
    /**
     * @VIA\Description("Индикатор отложенного документа")
     * @Assert\Type(type="bool")
     */
    public $isWaiting;

    /**
     * @Assert\Uuid
     */
    public $uuid;
    
}