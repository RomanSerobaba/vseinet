<?php 

namespace ReservesBundle\Bus\GoodsReleaseDoc\Query;

use AppBundle\Bus\Message\Message;
use AppBundle\Annotation as VIA;
use Symfony\Component\Validator\Constraints as Assert;

class GetListQuery extends Message 
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
     * @VIA\Description("Показывать документы имеющие перечисленные статусы. Если список пустой, то показываются документы во всех статусах.")
     * @Assert\Choice({"new", "completed"}, strict=true, multiple=true)
     */
    public $inStatuses;
    
    /**
     * @VIA\Description("Список типов отображаемых документов:
     * - client   выдача клиенту
     * - transit  перемещение между подразделениями
     * - movement перемещение внутри подразделения
     * - issue    отгрузка на ремонт
     * - freight  отгрузка в транспортную компанию
     * - courier  отгрузка курьеру
     * 
     * Если список пустой - отображаются документы всех типов")
     * @Assert\Choice({"client", "transit", "movement", "issue", "freight", "courier"}, strict=true, multiple=true)
     */
    public $inGoodsReleasesTypes;
    
    /**
     * @VIA\Description("Показывать документы имеющие один из перечисленных идентификаторов складов источников. Если список пустой, то фильтр не учитывается.")
     * @Assert\Type(type="array")
     * @Assert\All(@Assert\Type(type="integer"))
     */
    public $inGeoRoomsIds;
    
    /**
     * @VIA\Description("Показывать документы имеющие один из перечисленных идентификаторов складов приёмников. Если список пустой, то фильтр не учитывается.")
     * @Assert\Type(type="array")
     * @Assert\All(@Assert\Type(type="integer"))
     */
    public $inDestinationsRoomsIds;
    
    /**
     * @VIA\Description("Показывать закрытые (выполненные) документы")
     * @Assert\Type(type="boolean")
     */
    public $withCompleted;
    
    /**
     * @VIA\Description("Показывать отложенные документы")
     * @Assert\Type(type="boolean")
     */
    public $withWaiting;

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