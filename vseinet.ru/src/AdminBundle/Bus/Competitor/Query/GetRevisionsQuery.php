<?php 

namespace AdminBundle\Bus\Competitor\Query;

use AppBundle\Bus\Message\Message;
use Symfony\Component\Validator\Constraints as Assert;

class GetRevisionsQuery extends Message 
{
    /**
     * @Assert\NotBlank(message="Выберите товар")
     * @Assert\Type(type="numeric")
     */
    public $baseProductId;
}
