<?php

namespace PricingBundle\Bus\Competitors\Command;

use AppBundle\Bus\Message\Message;
use AppBundle\Annotation as VIA;
use Symfony\Component\Validator\Constraints as Assert;

class EditCommand extends Message
{
    /**
     * @Assert\Type(type="integer")
     * @VIA\Description("Конкурент")
     * @Assert\NotNull()
     */
    public $id;

    /**
     * @Assert\Type(type="string")
     * @VIA\Description("Ссылка")
     */
    public $link;

    /**
     * @Assert\Type(type="integer")
     * @VIA\Description("Поставщик")
     */
    public $supplierId;

    /**
     * @Assert\Type(type="integer")
     * @VIA\Description("Город")
     */
    public $geoCityId;

    /**
     * @Assert\Type(type="integer")
     * @VIA\Description("Улица")
     */
    public $geoStreetId;

    /**
     * @Assert\Type(type="string")
     * @VIA\Description("Дом")
     */
    public $house;

    /**
     * @Assert\Type(type="string")
     * @VIA\Description("Здание")
     */
    public $building;

    /**
     * @Assert\Type(type="integer")
     * @VIA\Description("Этаж")
     */
    public $floor;

    /**
     * @Assert\Type(type="boolean")
     * @VIA\Description("UTF кодировка")
     */
    public $isUtfCoding;
}