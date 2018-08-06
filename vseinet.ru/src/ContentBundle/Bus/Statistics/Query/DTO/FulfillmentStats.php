<?php 

namespace ContentBundle\Bus\Statistics\Query\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class FulfillmentStats
{    
    /**
     * @Assert\Type(type="integer")
     */
    public $count;

    /**
     * @Assert\Type(type="array<ContentBundle\Bus\Statistics\Query\DTO\FulfillmentLog>")
     */
    public $logs = [];


    public function __construct($count)
    {
        $this->count = $count;
    }
}