<?php

namespace GeoBundle\Bus\Geo\Query;

use AppBundle\Annotation as VIA;
use AppBundle\Bus\Message\Message;
use Symfony\Component\Validator\Constraints as Assert;

class GetRegionInfoQuery extends Message
{
    /**
     * @VIA\Description("Region id")
     * @Assert\NotBlank
     * @Assert\Type(type="integer")
     */
    public $id;
}