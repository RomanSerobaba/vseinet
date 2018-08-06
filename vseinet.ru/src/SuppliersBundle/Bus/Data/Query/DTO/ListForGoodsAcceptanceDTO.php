<?php 

namespace SuppliersBundle\Bus\Data\Query\DTO;

use AppBundle\Annotation as VIA;
use Symfony\Component\Validator\Constraints as Assert;

class ListForGoodsAcceptanceDTO
{
    /**
     * @VIA\Description("Идентификатор поставщика")
     * @Assert\Type(type="integer")
     */
    public $id;

    /**
     * @VIA\Description("Код поставщика")
     * @Assert\Type(type="string")
     */
    public $code;

    /**
     * @VIA\Description("Наименование поставщика")
     * @Assert\Type(type="string")
     */
    public $name;

    /**
     * @VIA\Description("Уникальные идентификаторы документов поставок")
     * @Assert\Type(type="array<integer>")
     */
    public $suppliesDocumentsIds;

    ////////////////////////////////////////////////////////////////////////

    public function setSuppliesDocumentsIds($inJson)
    {
        $inData = json_decode($inJson, true);
        $this->suppliesDocumentsIds = $inData;
    }

}