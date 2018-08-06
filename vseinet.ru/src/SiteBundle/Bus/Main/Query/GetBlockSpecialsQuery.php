<?php 

namespace SiteBundle\Bus\Main\Query;

use AppBundle\Bus\Message\Message;
use Symfony\Component\Validator\Constraints as Assert;

class GetBlockSpecialsQuery extends Message
{
    /**
     * @Assert\NotBlank
     * @Assert\Type(type="integer")
     */
    public $categoryId;

    /**
     * @Assert\NotBlank
     * @Assert\Type(type="integer")
     */
    public $count;
}
