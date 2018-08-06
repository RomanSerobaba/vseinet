<?php 

namespace FinanseBundle\Bus\BankOperationDoc\Query;

use AppBundle\Bus\Message\Message;
use AppBundle\Annotation as VIA;
use Symfony\Component\Validator\Constraints as Assert;

class ImportResultQuery extends Message 
{
    /**
     * @VIA\Description("UUID ранее выполненной команды.")
     * @Assert\Uuid
     * @Assert\NotBlank(message="UUID ранее выполненной команды обязательно должен быть указан.")
     */
    public $uuid;
}