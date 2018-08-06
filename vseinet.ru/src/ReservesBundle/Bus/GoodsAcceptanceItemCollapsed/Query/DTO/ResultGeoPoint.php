<?php 

namespace ReservesBundle\Bus\GoodsAcceptanceItemCollapsed\Query\DTO;

use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Annotation as VIA;

class ResultGeoPoint
{
    /**
     * @Assert\Type(type="integer")
     * @VIA\Description("Идентификатор склада")
     */
    private $geoPointId;

    /**
     * @Assert\Type(type="string")
     * @VIA\Description("Наименование склада")
     */
    private $geoPointName;

    /**
     * @Assert\Type(type="string")
     * @VIA\Description("Наименование склада")
     */
    private $geoPointCode;

    /**
     * @Assert\Type(type="integer")
     * @VIA\Description("Отпущено")
     */
    private $quantity;
    
    public function __construct($geoPointId, $geoPointName, $geoPointCode, $quantity)
    {
        $this->geoPointId = $geoPointId;
        $this->geoPointName = $geoPointName;
        $this->geoPointCode = $geoPointCode;
        $this->defectType = $defectType;
        $this->quantity = $quantity;
    }
}