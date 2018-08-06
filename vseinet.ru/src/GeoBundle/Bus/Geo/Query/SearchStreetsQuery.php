<?php 

namespace GeoBundle\Bus\Geo\Query;

use AppBundle\Bus\Message\Message;
use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Annotation as VIA;

class SearchStreetsQuery extends Message
{
    /**
     * @VIA\Description("Название улицы")
     * @Assert\NotBlank
     * @Assert\Type(type="string")
     */
    public $q;

    /**
     * @VIA\Description("City id")
     * @Assert\NotBlank
     * @Assert\Type(type="integer")
     */
    public $cityId;

    /**
     * @Assert\Type(type="integer")
     * @VIA\DefaultValue(20)
     */
    public $limit;
}