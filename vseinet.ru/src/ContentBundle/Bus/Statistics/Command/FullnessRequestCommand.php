<?php 

namespace ContentBundle\Bus\Statistics\Command;

use AppBundle\Bus\Message\Message;
use Symfony\Component\Validator\Constraints as Assert;

class FullnessRequestCommand extends Message 
{
    /**
     * @Assert\NotBlank(message="Значение subject не должно быть пустым")
     * @Assert\Choice({"images", "descriptions", "details", "brands"}, strict=true)
     */
    public $subject;
}