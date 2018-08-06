<?php 

namespace ContentBundle\Bus\Category\Query;

use AppBundle\Bus\Message\Message;
use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Annotation as VIA;

class TreeNormQuery extends Message
{
    /**
     * @VIA\Description("Глубина дерева")
     * @VIA\DefaultValue(2)
     * @Assert\Type(type="integer")
     */
    public $deep;

    /**
     * @VIA\Description("Идентификатор склада с остатками товара, входящего в выбираемые категории")
     * @Assert\Type(type="integer")
     */
    public $geoRoomId;
}