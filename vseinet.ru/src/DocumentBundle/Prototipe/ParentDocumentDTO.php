<?php
/**
 * @author Denis O. Konashonok
 */
namespace DocumentBundle\Prototipe;

use Symfony\Component\Validator\Constraints as Assert;
use DocumentBundle\SimpleTools\DocumentNameConverter;
use AppBundle\Annotation as VIA;

/**
 * Description of DocumentStatus
 *
 * @author denis
 */
class ParentDocumentDTO {

    /**
     * @VIA\Description("Уникальный идентификатор документа")
     * @Assert\Type(type="integer")
     */
    public $id;
    
    /**
     * @VIA\Description("Заголовок документа")
     * @Assert\Type(type="string")
     */
    public $title;
    
    /**
     * @VIA\Description("Тип родительского документа")
     * @Assert\Type(type="string")
     */
    public $documentType;
    
    public function __construct(int $id, string $title, string $documentType)
    {
        $this->id = $id;
        $this->title = $title;
        $this->documentType = DocumentNameConverter::TableName2Type($documentType);
    }

    
}
