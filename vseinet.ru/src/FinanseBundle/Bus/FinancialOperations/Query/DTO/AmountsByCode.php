<?php 

namespace FinanseBundle\Bus\FinancialOperations\Query\DTO;

use Symfony\Component\Validator\Constraints as Assert;
use DocumentBundle\Prototipe\ParentDocumentDTO;
use AppBundle\Annotation as VIA;

class AmountsByCode
{
    /**
     * @VIA\Description("Код типа платежа")
     * @Assert\Type(type="string")
     */
    public $payCode;
    
    /**
     * @VIA\Description("Сумма платежа")
     * @Assert\Type(type="integer")
     */
    public $amount;

    ///////////////////////////////////
    
    public function __construct($payCode, $amount)
    {
        $this->payCode = $payCode;
        $this->amount = $amount;
    }
}