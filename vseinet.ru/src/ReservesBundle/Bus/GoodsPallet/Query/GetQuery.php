<?php 
namespace ReservesBundle\Bus\GoodsPallet\Query;

use AppBundle\Bus\Message\Message;
use AppBundle\Annotation as VIA;
use Symfony\Component\Validator\Constraints as Assert;

class GetQuery extends Message 
{
    /**
     * @VIA\Description("Показывать не задействованые паллеты")
     * @Assert\Type(type="boolean")
     */
    public $withFree;
    
    /**
     * @VIA\Description("Показывать активные паллеты")
     * @Assert\Type(type="boolean")
     */
    public $withOpened;
    
    /**
     * @VIA\Description("Показывать закрытые паллеты")
     * @Assert\Type(type="boolean")
     */
    public $withClosed;
    
    /**
     * @VIA\Description("Показывать паллеты в пути")
     * @Assert\Type(type="boolean")
     */
    public $withInWay;
    
    /**
     * @VIA\Description("Показывать списанные паллеты")
     * @Assert\Type(type="boolean")
     */
    public $withWriteOff;

    
    ////////////////////////////////////////////////////////////
    //
    //  Интервал дат
    //
    
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
    
    
    ////////////////////////////////////////////////////////////
    //
    //  Пагинация
    //
    
   /**
     * @VIA\Description("Номер страницы")
     * @Assert\Type(type="integer")
     */
    public $page;
    
    /**
     * @VIA\Description("Длинна страницы")
     * @Assert\Type(type="integer")
     */
    public $limit;
    
}