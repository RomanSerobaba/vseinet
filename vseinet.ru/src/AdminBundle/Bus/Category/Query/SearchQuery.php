<?php 

namespace AdminBundle\Bus\Category\Query;

use AppBundle\Bus\Message\Message;
use Symfony\Component\Validator\Constraints as Assert;

class SearchQuery extends Message
{
    /**
     * @Assert\NotBlank(message="Введите поисковый запрос")
     * @Assert\Type(type="string")
     */
    public $q;

    /**
     * @Assert\type(type="integer")
     */
    public $limit = 10;
}
