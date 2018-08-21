<?php 

namespace AppBundle\Bus\Geo\Query;

use AppBundle\Bus\Message\Message;
use Symfony\Component\Validator\Constraints as Assert;

class GetCitiesQuery extends Message
{
    /**
     * @Assert\NotBlank(message="Выберите регион")
     * @Assert\Type(type="numeric")
     */
    public $geoRegionId;
}