<?php 

namespace ReservesBundle\Bus\Inventory\Query\DTO;

use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Annotation as VIA;

class DocumentList
{
    /**
     * @VIA\Description("Список документов претензий")
     * @Assert\Type(type="array<namespace ReservesBundle\Bus\Inventory\Query\DTO\Document>")
     */
    public $documents;

    /**
     * @VIA\Description("Общее количество элементов в списке документов претензий")
     * @Assert\Type(type="integer")
     */
    public $total;

    public function __construct(
            $documents = [], 
            $total)
    {
        $this->documents = $documents;
        $this->total = $total;
    }
    
}