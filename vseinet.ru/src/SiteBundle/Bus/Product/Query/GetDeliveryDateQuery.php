<?php 

namespace SiteBundle\Bus\Product\Query;

use AppBundle\Bus\Message\Message;
use Symfony\Component\Validator\Constraints as Assert;

class GetDeliveryDateQuery extends Message
{
    /**
     * @Assert\NotBlank
     * @Assert\Type(type="integer")
     */
    public $baseProductId;

    /**
     * @Assert\NotBlank
     * @Assert\Type(type="integer")
     */
    public $fromPointId;

    /**
     * @Assert\NotBlank
     * @Assert\Type(type="integer")
     */
    public $toPointId;
}
