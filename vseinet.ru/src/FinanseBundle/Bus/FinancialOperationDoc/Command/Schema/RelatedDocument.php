<?php 

namespace FinanseBundle\Bus\FinancialOperationDoc\Command\Schema;

use AppBundle\Bus\Message\Message;
use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Annotation as VIA;

class RelatedDocument extends Message
{    

    /**
     * @VIA\Description("Уникальный идентификатор рассчетного документа")
     * @Assert\NotBlank(message="Уникальный идентификатор рассчетного документа должен быть указан")
     * @Assert\Type(type="integer")
     */
    public $documentId;
    
    /**
     * @VIA\Description("Сумма зачтенная к рассчету")
     * @Assert\Type(type="integer")
     */
    public $amount;
    
}
