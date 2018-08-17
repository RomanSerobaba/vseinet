<?php 

namespace AppBundle\Bus\Favorite\Query\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class Info 
{
    /**
     * @Assert\Type(type="integer")
     */
    public $count;

    /**
     * @Assert\Type(type="array<integer>")
     */
    public $ids;


    public function __construct(array $ids)
    {
        $this->count = count($ids);
        $this->ids = array_combine($ids, $ids);
    }
}
