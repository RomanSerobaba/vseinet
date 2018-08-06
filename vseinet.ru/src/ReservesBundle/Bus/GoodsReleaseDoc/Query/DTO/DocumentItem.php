<?php 

namespace ReservesBundle\Bus\GoodsReleaseDoc\Query\DTO;

use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Annotation as VIA;

class DocumentItem
{
    use \DocumentBundle\Prototipe\DocumentDTO;
    
    /**
     * @VIA\Description("Тип документа отгрузки:
     * - client      Отпуск товара клиенту
     * - transit     Отгрузка в другое подразделение
     * - movement    Отгрузка внутри подразделения
     * - issue       Отгрузка на ремонт
     * - freight     Отгрузка в транспортную компанию
     * - courier     Отгрузка курьеру")
     * @Assert\Type(type="string")
     */
    public $goodsReleaseType;

    /**
     * @VIA\Description("Идентификатор геоточки")
     * @Assert\Type(type="integer")
     */
    public $geoRoomId;
    
    /**
     * @VIA\Description("Наименование геоточки")
     * @Assert\Type(type="string")
     */
    public $geoRoomName;
    
    /**
     * @VIA\Description("Идентификатор геоточки получателя")
     * @Assert\Type(type="integer")
     */
    public $destinationRoomId;

    /**
     * @VIA\Description("Наиемнование геоточки получателя")
     * @Assert\Type(type="string")
     */
    public $destinationRoomName;

    /**
     * @VIA\Description("Отложенная выдача")
     * @Assert\Type(type="boolean")
     */
    public $isWaiting;
   
}