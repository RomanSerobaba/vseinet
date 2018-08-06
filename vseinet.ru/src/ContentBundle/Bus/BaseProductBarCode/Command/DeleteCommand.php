<?php 

namespace ContentBundle\Bus\BaseProductBarCode\Command;

use AppBundle\Bus\Message\Message;
use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Annotation as VIA;

class DeleteCommand extends Message
{    
    /**
     * @VIA\Description("Идентификатор штрихкода")
     * @Assert\NotBlank(message="Идентификатор штрихкода должен быть указан.")
     * @Assert\Type(type="integer")
     */
    public $id;

}