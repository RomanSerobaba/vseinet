<?php 

namespace ContentBundle\Bus\DetailValueAudit\Query\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class DetailValues
{    
    /**
     * @Assert\Type(type="array<ContentBundle\Bus\DetailValueAudit\Query\DTO\DetailValue>")
     */
    public $values;

    /**
     * @Assert\Type(type="array<ContentBundle\Bus\DetailValueAudit\Query\DTO\DetailValueAlias>")
     */
    public $aliases;


    public function __construct($values, $aliases)
    {
        $this->values = array_values($values);
        $this->aliases = array_values($aliases);
    }
}