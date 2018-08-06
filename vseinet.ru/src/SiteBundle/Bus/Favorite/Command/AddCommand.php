<?php 

namespace SiteBundle\Bus\Favorite\Command;

use AppBundle\Bus\Message\Message;
use Symfony\Component\Validator\Constraints as Assert;

class AddCommand extends Message
{
    /**
     * @Assert\NotBlank(message="Выберите товар")
     * @Assert\Type(type="integer")
     */
    public $id;
}
