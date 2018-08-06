<?php 

namespace DocumentBundle\Bus\Comment\Query;

use AppBundle\Bus\Message\Message;
use AppBundle\Annotation as VIA;
use Symfony\Component\Validator\Constraints as Assert;

class ListQuery extends Message 
{
    /**
     * @VIA\Description("Идентификатор документа")
     * @Assert\NotBlank(message="Идентификатор документа должен быть указан")
     * @Assert\Type(type="integer")
     */
    public $documentId;

}