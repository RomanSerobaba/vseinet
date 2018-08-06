<?php 

namespace ShopBundle\Bus\Banner\Command;

use AppBundle\Bus\Message\Message;
use AppBundle\Annotation as VIA;
use Symfony\Component\Validator\Constraints as Assert;

class DeleteCommand extends Message
{
    /**
     * @VIA\Description("Баннер id")
     * @Assert\NotBlank(message="Баннер не указан")
     * @Assert\Type(type="integer")
     */
    public $id;
}