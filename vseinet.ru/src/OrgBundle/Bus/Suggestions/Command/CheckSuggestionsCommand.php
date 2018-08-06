<?php 

namespace OrgBundle\Bus\Suggestions\Command;

use AppBundle\Bus\Message\Message;
use AppBundle\Annotation as VIA;
use Symfony\Component\Validator\Constraints as Assert;

class CheckSuggestionsCommand extends Message
{
    /**
     * @VIA\Description("Suggestion id")
     * @Assert\NotBlank
     * @Assert\Type(type="integer")
     */
    public $id;

    /**
     * @VIA\Description("Обработан")
     * @Assert\Type(type="boolean")
     */
    public $isCheck;
}