<?php 

namespace GeoBundle\Bus\Geo\Query;

use AppBundle\Annotation as VIA;
use AppBundle\Bus\Message\Message;
use Symfony\Component\Validator\Constraints as Assert;

class SearchCitiesQuery extends Message
{
    /**
     * @VIA\Description("Название города")
     * @Assert\NotBlank
     * @Assert\Type(type="string")
     */
    public $q;

    /**
     * @VIA\Description("Region id")
     * @Assert\Type(type="integer")
     */
    public $regionId;

    /**
     * @Assert\Type(type="integer")
     * @VIA\DefaultValue(20)
     */
    public $limit;
}