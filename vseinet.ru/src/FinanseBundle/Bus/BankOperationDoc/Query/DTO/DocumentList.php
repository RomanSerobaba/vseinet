<?php 

namespace FinanseBundle\Bus\BankOperationDoc\Query\DTO;

use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Annotation as VIA;

class DocumentList
{
    /**
     * @VIA\Description("Список документов")
     * @Assert\Type(type="array<FinanseBundle\Bus\BankOperationDoc\Query\DTO\Documents>")
     */
    public $documents;

    /**
     * @VIA\Description("Общее количество элементов в списке документов")
     * @Assert\Type(type="integer")
     */
    public $total;

    public function __construct($documents = [], $total)
    {
        $this->documents = $documents;
        $this->total = $total;
    }
    
}