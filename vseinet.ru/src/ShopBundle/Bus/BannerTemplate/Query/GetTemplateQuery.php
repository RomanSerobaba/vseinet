<?php 

namespace ShopBundle\Bus\BannerTemplate\Query;

use AppBundle\Bus\Message\Message;
use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Annotation as VIA;

class GetTemplateQuery extends Message
{
    /**
     * @VIA\Description("Шаблон id")
     * @Assert\NotBlank(message="Шаблон не указан")
     * @Assert\Type(type="integer")
     */
    public $id;
}