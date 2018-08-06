<?php
namespace ReservesBundle\Bus\GoodsReleaseDoc\Query\DTO;

use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Annotation as VIA;

/**
 * Description of DocumentStatus
 *
 * @author denis
 */
class Statuses {

    /**
     * @VIA\Description("Идентификатор")
     * @Assert\Type(type="integer")
     */
    public $id;
    
    /**
     * @VIA\Description("Код статуса")
     * @Assert\Type(type="string")
     */
    public $statusCode;
    
    /**
     * @VIA\Description("Наименование")
     * @Assert\Type(type="string")
     */
    public $name;
    
    /**
     * @VIA\Description("Прищнак использования статуса")
     * @Assert\Type(type="boolean")
     */
    public $active;
    
    /**
     * @VIA\Description("Коды возможных следующих статусов")
     * @Assert\Type(type="array<string>")
     */
    public $availableNewStatusCode;
    
    /**
     * @VIA\Description("Признак конечного статуса")
     * @Assert\Type(type="boolean")
     */
    public $completing;
    

    public function setAvailableNewStatusCode($inJson){
        $inData = json_decode($inJson, true);
        if (!empty($inData)) {
            $this->availableNewStatusCode = $inData;
        }else{
            $this->availableNewStatusCode = [];
        }
    }
}
