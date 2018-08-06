<?php 

namespace SiteBundle\Bus\Geo\Query\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class Point
{
    /**
     * @Assert\Type(type="integer")
     */
    public $geoCityId;

    /**
     * @Assert\Type(type="boolean")
     */
    public $hasRetail = false;

    /**
     * @Assert\Type(type="boolean")
     */
    public $hasDelivery = false;

    /**
     * @Assert\Type(type="boolean")
     */
    public $isNew = false;
    

    public function __construct($geoCityId, $hasRetail, $hasDelivery, $isNew)
    {
        $this->geoCityId = $geoCityId;
        $this->hasRetail = $hasRetail;
        $this->hasDelivery = $hasDelivery;
        $this->isNew = $isNew;
    }
}