<?php 

namespace ReservesBundle\Bus\GoodsAcceptance\Query;

use AppBundle\Bus\Message\Message;
use AppBundle\Annotation as VIA;
use Symfony\Component\Validator\Constraints as Assert;

class ListQuery extends Message 
{
    
    /**
     * @VIA\Description("Дата начала выборки (включая)")
     * @Assert\Date()
     */
    public $fromDate;
    
    /**
     * @VIA\Description("Дата завершения выборки (включая)")
     * @Assert\Date()
     */
    public $toDate;
    
    /**
     * @VIA\Description("Показывать и закрытые (завершённые/архивные) документы")
     * @Assert\Type(type="boolean")
     * @VIA\DefaultValue(true)
     */
    public $withCompleted;
    
    /**
     * @VIA\Description("Фильтр складов. Показывать со складами из списка. Массив идентификаторов складов в формате json: [1,2,3,4]")
     * @Assert\Type(type="array")
     * @Assert\All(@Assert\Type(type="integer"))
     */
    public $inGeoRoomsIds;
    
    /**
     * @VIA\Description("Фильтр складов-отправителей. Показывать со складами-отправиьелями из списка. Массив идентификаторов складов в формате json: [1,2,3,4]")
     * @Assert\Type(type="array")
     * @Assert\All(@Assert\Type(type="integer"))
     */
    public $inGeoRoomsSourcesIds;
    
    /**
     * @VIA\Description("Фильтр статусов документа. Показывать документы имеющие перечисленные статусы. Если список пустой, то показываются документы во всех статусах.")
     * @Assert\Choice({"new", "completed"}, strict=true, multiple=true)
     */
    public $inStatuses;
    
    /**
     * @VIA\Description("Фильтр авторов документа: Показывать документы указанных авторов. Массив идентификаторов авторов в формате json: [1,2,3,4]")
     * @Assert\Type(type="array")
     * @Assert\All(@Assert\Type(type="integer"))
     */
    public $inCreatedBy;
    
    /**
     * @VIA\Description("Номер страницы")
     * @Assert\Type(type="integer")
     * @VIA\DefaultValue(1)
     */
    public $page;
    
    /**
     * @VIA\Description("Длинна страницы")
     * @Assert\Type(type="integer")
     * @VIA\DefaultValue(50)
     */
    public $limit;
    
}