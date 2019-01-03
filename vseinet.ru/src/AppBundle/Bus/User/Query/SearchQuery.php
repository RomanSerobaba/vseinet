<?php

namespace AppBundle\Bus\User\Query;

use AppBundle\Bus\Message\Message;
use Symfony\Component\Validator\Constraints as Assert;

class SearchQuery extends Message
{
    /**
     * @Assert\NotBlank(message="Не указан поисковый запрос")
     * @Assert\Type(type="string")
     */
    public $q;

    /**
     * @Assert\NotBlank(message="Не указано поле для поиска")
     * @Assert\Type(type="string")
     */
    public $field;

    /**
     * @Assert\Type(type="integer")
     */
    public $limit = 10;
}
