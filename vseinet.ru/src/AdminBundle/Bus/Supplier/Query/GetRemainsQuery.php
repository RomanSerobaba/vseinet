<?php 

namespace AdminBundle\Bus\Supplier\Query;

use AppBundle\Bus\Message\Message;
use Symfony\Component\Validator\Constraints as Assert;

class GetRemainsQuery extends Message
{
    /**
     * @Assert\NotBlank(message="Выберите товар")
     * @Assert\Type(type="numeric")
     */
    public $baseProductId;
}
