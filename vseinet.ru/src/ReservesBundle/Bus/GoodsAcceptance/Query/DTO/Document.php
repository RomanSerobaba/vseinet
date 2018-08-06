<?php 

namespace ReservesBundle\Bus\GoodsAcceptance\Query\DTO;

use Symfony\Component\Validator\Constraints as Assert;
use DocumentBundle\Prototipe\ParentDocumentDTO;
use AppBundle\Annotation as VIA;

class Document
{
    use \DocumentBundle\Prototipe\DocumentDTO;
 
    /**
     * @Assert\Type(type="integer")
     * @VIA\Description("Идентификатор геоточки")
     */
    public $geoRoomId;
    
    /**
     * @Assert\Type(type="string")
     * @VIA\Description("Наименование геоточки")
     */
    public $geoRoomName;
    
    /**
     * @Assert\Type(type="integer")
     * @VIA\Description("Идентификатор геоточки отправителя")
     */
    public $geoRoomSourceId;

    /**
     * @Assert\Type(type="string")
     * @VIA\Description("Наиемнование геоточки отправителя")
     */
    public $geoRoomSourceName;

    /**
     * @VIA\Description("Перечень заказов поставщику, оприходованных в этом поступлении")
     * @Assert\Type(type="array<DocumentBundle\Prototipe\ParentDocumentDTO>")
     */
    public $supplyiesDocuments;

    public function setSupplyiesDocuments($inJson)
    {
        
        $inData = json_decode($inJson, true);
        
        $this->supplyiesDocuments = [];
        
        if (!empty($inData)) {
            foreach ($inData as $value) {
                $this->supplyiesDocuments[] = new ParentDocumentDTO($value['id'], $value['title'], $value['document_type']);                
            }
        }
        
    }

}