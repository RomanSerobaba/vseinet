<?php

namespace PricingBundle\Bus\Competitors\Command;

use AppBundle\Bus\Message\Message;
use AppBundle\Annotation as VIA;
use Symfony\Component\Validator\Constraints as Assert;

class AddCommand extends Message
{
    /**
     * @Assert\Type(type="string")
     * @VIA\Description("Наименование")
     * @Assert\NotBlank
     */
    public $name;

    /**
     * @Assert\Type(type="string")
     * @VIA\Description("Тип")
     * @Assert\Choice({"retail", "pricelist", "site"}, strict=true)
     * @Assert\NotBlank
     */
    public $typeCode;

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