<?php 

namespace ReservesBundle\Bus\GoodsPallet\Query;

use AppBundle\Bus\Message\Message;
use AppBundle\Annotation as VIA;
use Symfony\Component\Validator\Constraints as Assert;

class GetItemQuery extends Message 
{
    /**
     * @VIA\Description("Идентификатор паллеты")
     * @Assert\NotBlank(message="Идентификатор паллеты должен быть указан")
     * @Assert\Type(type="integer")
     */
    public $id;
}