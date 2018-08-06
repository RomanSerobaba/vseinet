<?php 

namespace FinanseBundle\Bus\ExpenseSimpleDoc\Query;

use AppBundle\Bus\Message\Message;
use AppBundle\Annotation as VIA;
use Symfony\Component\Validator\Constraints as Assert;

class ItemQuery extends Message 
{
    /**
     * @VIA\Description("Идентификатор документа")
     * @Assert\Type(type="integer")
     */
    public $id;
}