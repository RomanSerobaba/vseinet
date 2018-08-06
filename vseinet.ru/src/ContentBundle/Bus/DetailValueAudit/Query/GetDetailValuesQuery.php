<?php 

namespace ContentBundle\Bus\DetailValueAudit\Query;

use AppBundle\Bus\Message\Message;
use Symfony\Component\Validator\Constraints as Assert;

class GetDetailValuesQuery extends Message 
{
    /**
     * @Assert\NotBlank(message="Значение detailId не должно быть пустым")
     * @Assert\Type(type="integer")
     */
    public $detailId;
}