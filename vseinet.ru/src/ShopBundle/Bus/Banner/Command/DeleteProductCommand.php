<?php 

namespace ShopBundle\Bus\Banner\Command;

use AppBundle\Bus\Message\Message;
use AppBundle\Annotation as VIA;
use Symfony\Component\Validator\Constraints as Assert;

class DeleteProductCommand extends Message
{
    /**
     * @VIA\Description("Идентификатор товара из баннера")
     * @Assert\NotBlank(message="Товар не указан")
     * @Assert\Type(type="integer")
     */
    public $id;
}