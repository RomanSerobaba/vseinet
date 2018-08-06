<?php 

namespace ShopBundle\Bus\BannerTemplate\Command;

use AppBundle\Bus\Message\Message;
use AppBundle\Annotation as VIA;
use Symfony\Component\Validator\Constraints as Assert;

class DeleteCommand extends Message
{
    /**
     * @VIA\Description("Шаблон id")
     * @Assert\NotBlank(message="Шаблон не указан")
     * @Assert\Type(type="integer")
     */
    public $id;
}