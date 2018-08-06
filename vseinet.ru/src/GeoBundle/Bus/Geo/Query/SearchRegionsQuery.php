<?php 

namespace GeoBundle\Bus\Geo\Query;

use AppBundle\Annotation as VIA;
use AppBundle\Bus\Message\Message;
use Symfony\Component\Validator\Constraints as Assert;

class SearchRegionsQuery extends Message
{
    /**
     * @VIA\Description("Название региона")
     * @Assert\Type(type="string")
     */
    public $q;
}