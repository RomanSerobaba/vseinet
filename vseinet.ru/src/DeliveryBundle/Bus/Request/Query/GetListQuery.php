<?php 

namespace DeliveryBundle\Bus\Request\Query;

use AppBundle\Bus\Message\Message;
use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Annotation as VIA;
use AppBundle\Validator\Constraints as VIC;

class GetListQuery extends Message
{
    /**
     * @Assert\Type(type="boolean")
     * @VIA\Description("Только заявки, не оформленные на доставку")
     */
    public $isFree;

    /**
     * @Assert\Type(type="integer")
     * @VIA\Description("Номер доставки")
     */
    public $deliveryNumber;

    /**
     * @Assert\Type(type="string")
     * @VIA\Description("Тип доставки")
     */
    public $type;

    /**
     * @Assert\Type(type="integer")
     * @VIA\Description("Идентификатор точки")
     */
    public $pointId;

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