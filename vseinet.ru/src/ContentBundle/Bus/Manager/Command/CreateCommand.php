<?php 

namespace ContentBundle\Bus\Manager\Command;

use AppBundle\Bus\Message\Message;
use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Annotation as VIA;

class CreateCommand extends Message 
{
    /**
     * @Assert\NotBlank(message="Значение userId не должно быть пустым")
     * @Assert\Type(type="integer")
     */
    public $userId;

    /**
     * @VIA\Description("Если оставить пустым, группа будет сформированна согласно структуре организации")
     * @Assert\Type(type="integer")
     */
    public $groupId;
}