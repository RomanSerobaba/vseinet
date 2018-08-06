<?php 

namespace FinanseBundle\Bus\ItemOfExpenses\Query\DTO;

use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Annotation as VIA;

class GroupDTO
{
    /**
     * @VIA\Description("Идентификатор группы сатей расхода")
     * @Assert\Type(type="integer")
     */
    public $id;

    /**
     * @VIA\Description("Идентификатор родительской группы сатей расхода")
     * @Assert\Type(type="integer")
     */
    public $pid;
    /**
     * @VIA\Description("Идентификаторы сатей расхода")
     * @Assert\Type(type="array<integer>")
     */
    public $childrenIds;

    /**
     * @VIA\Description("Наименование группы статьи расхода")
     * @Assert\Type(type="стринг")
     */
    public $name;

    public function __construct($inData)
    {
        $this->childrenIds = [];
        
        if (is_int($inData['id'])) $this->id = $inData['id'];
        if (is_int($inData['pid'])) $this->pid = $inData['pid'];
        if (is_array($inData['children_ids'])) $this->childrenIds = $inData['children_ids'];
        if (is_scalar($inData['name'])) $this->name = $inData['name'];
    }
    
}