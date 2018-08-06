<?php 

namespace SiteBundle\Bus\ContentPage\Query;

use AppBundle\Bus\Message\Message;
use Symfony\Component\Validator\Constraints as Assert;

class GetQuery extends Message
{
    /**
     * @Assert\NotBlank(message="Не указано наименование ресурса")
     * @Assert\Type(type="string")
     */
    public $slug;
}