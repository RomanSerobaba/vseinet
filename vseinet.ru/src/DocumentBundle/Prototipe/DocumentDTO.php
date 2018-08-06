<?php 
/**
 * @author Denis O. Konashonok
 */
namespace DocumentBundle\Prototipe;

use Symfony\Component\Validator\Constraints as Assert;
use DocumentBundle\Prototipe\ParentDocumentDTO;
use AppBundle\Annotation as VIA;

trait DocumentDTO
{
    /**
     * @VIA\Description("Универсальный идентификатор документа")
     * @Assert\Type(type="integer")
     */
    public $id;

    /**
     * @VIA\Description("Номер документа")
     * @Assert\Type(type="integer")
     */
    public $number;

    /**
     * @VIA\Description("Документ-родитель")
     * @Assert\Type(type="DocumentBundle\Prototipe\ParentDocumentDTO")
     */
    public $parentDoc;
    
    /**
     * @VIA\Description("Дата создания документа")
     * @Assert\DateTime
     * @Assert\Type(type="datetime")
     */
    public $createdAt;
    
    /**
     * @VIA\Description("Идентификатор автора")
     * @Assert\Type(type="integer")
     */
    public $createdBy;
    
    /**
     * @VIA\Description("Наименование автора")
     * @Assert\Type(type="string")
     */
    public $createdName;
    
    /**
     * @VIA\Description("Дата закрытия документа")
     * @Assert\DateTime
     * @Assert\Type(type="datetime")
     */
    public $completedAt;
    
    /**
     * @VIA\Description("Идентификатор пользователя закрывшего документ")
     * @Assert\Type(type="integer")
     */
    public $completedBy;
    
    /**
     * @VIA\Description("Наименование пользователя закрывшего документ")
     * @Assert\Type(type="string")
     */
    public $completedName;
    
    /**
     * @VIA\Description("Дата проведения документа")
     * @Assert\DateTime
     * @Assert\Type(type="datetime")
     */
    public $registeredAt;
    
    /**
     * @VIA\Description("Идентификатор пользователя проведшего документ")
     * @Assert\Type(type="integer")
     */
    public $registeredBy;
    
    /**
     * @VIA\Description("Наименование пользователя проведшего документ")
     * @Assert\Type(type="string")
     */
    public $registeredName;
    
    /**
     * @VIA\Description("Статус документа")
     * @Assert\Type(type="string")
     */
    public $statusCode;
    
    /**
     * @VIA\Description("Человекочитаемый заголовок документа")
     * @Assert\Type(type="string")
     */
    public $title;
    
    public function setParentDoc($inJson)
    {
        
        $inData = json_decode($inJson, true);
        if (!empty($inData)) {
            $this->parentDoc = new ParentDocumentDTO($inData['id'], $inData['title'], $inData['document_type']);
        }
        
    }

}