<?php 

namespace SiteBundle\Bus\ContentPage\Query;

use AppBundle\Bus\Message\Message;
use Symfony\Component\Validator\Constraints as Assert;

class GetListQuery extends Message
{
    /**
     * @Assert\NotBlank(message="Не указан тип ресурсов")
     * @Assert\Type(type="string")
     */
    public $type;
}