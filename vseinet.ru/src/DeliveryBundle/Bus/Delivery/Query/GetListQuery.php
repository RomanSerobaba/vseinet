<?php 

namespace DeliveryBundle\Bus\Delivery\Query;

use AppBundle\Bus\Message\Message;
use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Annotation as VIA;
use AppBundle\Validator\Constraints as VIC;

class GetListQuery extends Message
{
    /**
     * @Assert\Type(type="integer")
     * @VIA\Description("Номер документа доставки")
     */
    public $number;
    
    /**
     * @Assert\Type(type="string")
     * @VIA\Description("Код статуса документа")
     */
    public $status;

    /**
     * @Assert\Type(type="datetime")
     * @VIA\Description("Самая ранняя дата отгрузки")
     */
    public $shippedFrom;

    /**
     * @Assert\Type(type="datetime")
     * @VIA\Description("Самая поздняя дата отгрузки")
     */
    public $shippedTo;

    /**
     * @Assert\Type(type="datetime")
     * @VIA\Description("Самая ранняя дата закрытия документа")
     */
    public $completedFrom;

    /**
     * @Assert\Type(type="datetime")
     * @VIA\Description("Самая поздняя дата закрытия документа")
     */
    public $completedTo;

    /**
     * @VIC\Enum("AppBundle\Enum\DeliveryTypeCode")
     * @VIA\Description("Тип документа")
     */
    public $type;

    /**
     * @Assert\Type(type="integer")
     * @VIA\Description("Идентификатор грузоперевозчика")
     */
    public $transportCompanyId;

    /**
     * @Assert\Type(type="integer")
     * @VIA\Description("Страница списка доставок")
     * @VIA\DefaultValue(1)
     */
    public $page;

    /**
     * @Assert\Type(type="integer")
     * @VIA\Description("Количество элементов на странице списка доставок")
     * @VIA\DefaultValue(50)
     */
    public $limit;
    
}