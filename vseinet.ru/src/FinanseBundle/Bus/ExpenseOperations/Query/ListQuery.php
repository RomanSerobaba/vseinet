<?php 

namespace FinanseBundle\Bus\ExpenseOperations\Query;

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
     * @VIA\Description("Фильтр представительств")
     * @Assert\Type(type="array")
     * @Assert\All(@Assert\Type(type="integer"))
     */
    public $inRepresentativesIds;
    
    /**
     * @VIA\Description("Фильтр статусов документов.")
     * @Assert\Choice({"new", "active", "wait", "rejected", "completed"}, strict=true, multiple=true)
     */
    public $inStatuses;
    
    /**
     * @VIA\Description("Фильтр по авторам документов")
     * @Assert\Type(type="array")
     * @Assert\All(@Assert\Type(type="integer"))
     */
    public $inCreatedBy;
    
    /**
     * @VIA\Description("Фильтр по источникам финансов")
     * @Assert\Type(type="array")
     * @Assert\All(@Assert\Type(type="integer"))
     */
    public $inFinancialResourcesIds;
    
    /**
     * @VIA\Description("Фильтр по финансовым контрагентам")
     * @Assert\Type(type="array")
     * @Assert\All(@Assert\Type(type="integer"))
     */
    public $inFinancialCounteragentsIds;
    
    /**
     * @VIA\Description("Фильтр по обюорудованию/автомобилям")
     * @Assert\Type(type="array")
     * @Assert\All(@Assert\Type(type="integer"))
     */
    public $inEquipmentsIds;
    
    /**
     * @VIA\Description("Фильтр по статям расхода")
     * @Assert\Type(type="array")
     * @Assert\All(@Assert\Type(type="integer"))
     */
    public $inItemsOfEquipmentsIds;
    
    /**
     * @VIA\Description("Фильтр по типам документов")
     * @Assert\Choice({"simpleExpenses", "accountableExpenses", "supplierOrderExpenses", "buyerOrderExpenses"}, strict=true, multiple=true)
     */
    public $inDocumentType;
    
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