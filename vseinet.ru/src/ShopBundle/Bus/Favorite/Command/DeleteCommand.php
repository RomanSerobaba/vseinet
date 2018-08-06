<?php 

namespace ShopBundle\Bus\Favorite\Command;

use AppBundle\Bus\Message\Message;
use AppBundle\Annotation as VIA;
use Symfony\Component\Validator\Constraints as Assert;

class DeleteCommand extends Message
{
    /**
     * @VIA\Description("Product id")
     * @Assert\NotBlank(message="Product не указан")
     * @Assert\Type(type="integer")
     */
    public $id;
}