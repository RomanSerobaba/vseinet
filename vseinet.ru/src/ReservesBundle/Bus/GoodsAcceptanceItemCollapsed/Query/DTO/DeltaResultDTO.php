<?php 

namespace ReservesBundle\Bus\GoodsAcceptanceItemCollapsed\Query\DTO;

use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Annotation as VIA;

class DeltaResultDTO
{
    
    /**
     * @VIA\Description("Идентификатор конечного получателя")
     * @Assert\Type(type="integer")
     */
    public $geoPointId;
    
    /**
     * @VIA\Description("Код конечного получателя")
     * @Assert\Type(type="string")
     */
    public $geoPointCode;

    /**
     * @VIA\Description("Название конечного получателя")
     * @Assert\Type(type="string")
     */
    public $geoPointName;

    /**
     * @VIA\Description("Принято")
     * @Assert\Type(type="integer")
     */
    public $quantity;
    
    public function __construct($geoPointId, $geoPointCode, $geoPointName, $quantity)
    {

        $this->geoPointId = $geoPointId;
        $this->geoPointCode = $geoPointCode;
        $this->geoPointName = $geoPointName;
        $this->quantity = $quantity;
                
    }
    
}