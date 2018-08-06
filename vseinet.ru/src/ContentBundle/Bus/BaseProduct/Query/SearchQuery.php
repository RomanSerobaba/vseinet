<?php 

namespace ContentBundle\Bus\BaseProduct\Query;

use AppBundle\Bus\Message\Message;
use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Annotation as VIA;

class SearchQuery extends Message
{
    /**
     * @Assert\NotBlank(message="Искомая строка не должна быть пустой")
     * @Assert\Type(type="string")
     * @VIA\Description("Простой поисковый запрос")
     */
    public $q;

    /**
     * @Assert\Type(type="integer")
     * @VIA\DefaultValue(25)
     */
    public $limit;
}