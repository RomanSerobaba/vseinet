<?php 

namespace FinanseBundle\Bus\ItemOfExpenses\Query\DTO;

use Symfony\Component\Validator\Constraints as Assert;
use DocumentBundle\Prototipe\ParentDocumentDTO;
use DocumentBundle\SimpleTools\DocumentNameConverter;
use AppBundle\Annotation as VIA;

class ItemDTO
{
    use \DocumentBundle\Prototipe\DocumentDTO;

    /**
     * @VIA\Description("Идентификатор статьи расхода")
     * @Assert\Type(type="integer")
     */
    public $id;

    /**
     * @VIA\Description("Идентификатор группы ситатьи расхода")
     * @Assert\Type(type="integer")
     */
    public $pid;

    /**
     * @VIA\Description("Наименование статьи расхода")
     * @Assert\Type(type="string")
     */
    public $name;

    public function __construct($inData)
    {
        $this->childrenIds = [];
        
        if (is_int($inData['id'])) $this->id = $inData['id'];
        if (is_int($inData['pid'])) $this->pid = $inData['pid'];
        if (is_scalar($inData['name'])) $this->name = $inData['name'];
    }
    
}