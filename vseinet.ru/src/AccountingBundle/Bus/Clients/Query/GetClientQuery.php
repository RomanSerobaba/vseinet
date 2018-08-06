<?php 

namespace AccountingBundle\Bus\Clients\Query;

use AppBundle\Bus\Message\Message;
use AppBundle\Annotation as VIA;
use Symfony\Component\Validator\Constraints as Assert;

class GetClientQuery extends Message
{
    /**
     * @Assert\NotBlank
     * @VIA\Description("User id")
     */
    public $id;
}