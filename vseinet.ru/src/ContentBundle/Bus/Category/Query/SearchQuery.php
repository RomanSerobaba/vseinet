<?php 

namespace ContentBundle\Bus\Category\Query;

use AppBundle\Bus\Message\Message;
use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Annotation as VIA;

class SearchQuery extends Message
{
    /**
     * @Assert\NotBlank(message="Значение q не должно быть пустым")
     * @Assert\Type(type="string")
     * @VIA\Description("Поисковый запрос")
     */
    public $q;

    /**
     * @Assert\Type(type="integer")
     * @VIA\DefaultValue(10)
     */
    public $limit;
}