<?php 

namespace SupplyBundle\Bus\Data\Query;

use AppBundle\Bus\Message\Message;
use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Annotation as VIA;

class GetCandidatesQuery extends Message
{
    /**
     * @VIA\Description("Invoice id")
     * @Assert\NotBlank
     * @Assert\Type(type="integer")
     */
    public $id;
}