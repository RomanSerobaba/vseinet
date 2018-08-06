<?php

namespace FinanseBundle\Bus\ExpenseOperations\Query\DTO;

use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Annotation as VIA;

class ListExpensesDTO
{

    /**
     * @VIA\Description("Список документов")
     * @Assert\Type(type="array<FinanseBundle\Bus\ExpenseOperations\Query\DTO\ListExpensesDocumentDTO>")
     */
    public $documents;

    /**
     * @VIA\Description("Общее количество элементов в списке")
     * @Assert\Type(type="integer")
     */
    public $total;

    public function __construct($documents = [], $total)
    {
        $this->documents = $documents;
        $this->total = $total;
    }

}
