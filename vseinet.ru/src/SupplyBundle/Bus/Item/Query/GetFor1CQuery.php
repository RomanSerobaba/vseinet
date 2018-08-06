<?php 

namespace SupplyBundle\Bus\Item\Query;

use AppBundle\Bus\Message\Message;
use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Annotation as VIA;

class GetFor1CQuery extends Message
{
    /**
     * @Assert\NotBlank(message="Value of 'id' should not be blank")
     * @VIA\Description("Supplier invoice id")
     */
    public $id;
}