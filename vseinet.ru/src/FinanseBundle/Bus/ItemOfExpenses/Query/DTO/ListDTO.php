<?php 

namespace FinanseBundle\Bus\ItemOfExpenses\Query\DTO;

use Symfony\Component\Validator\Constraints as Assert;
use DocumentBundle\Prototipe\ParentDocumentDTO;
use AppBundle\Annotation as VIA;

class ListDTO
{
    /**
     * @VIA\Description("Список групп статей расхода")
     * @Assert\Type(type="array<FinanseBundle\Bus\ItemOfExpense\Query\DTO\Group>")
     */
    public $groups;
    
    /**
     * @VIA\Description("Список статей расхода")
     * @Assert\Type(type="array<FinanseBundle\Bus\ItemOfExpense\Query\Item>")
     */
    public $items;

    ///////////////////////////////////
    
    public function setGroups($inJson)
    {
        $this->groups = [];
        $inData = json_decode($inJson, true);
        if (!empty($inData)) {
            foreach ($inData as $value) {
                $this->groups[] = new GroupDTO($value);
            }
        }
    }
    
    public function setItems($inJson)
    {
        $this->items = [];
        $inData = json_decode($inJson, true);
        if (!empty($inData)) {
            foreach ($inData as $value) {
                $this->items[] = new ItemDTO($value);
            }
        }
    }
}