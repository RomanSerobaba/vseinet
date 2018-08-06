<?php

namespace GeoBundle\Bus\Geo\Query;

use AppBundle\Annotation as VIA;
use AppBundle\Bus\Message\Message;
use Symfony\Component\Validator\Constraints as Assert;

class GetStreetInfoQuery extends Message
{
    /**
     * @VIA\Description("Street id")
     * @Assert\NotBlank
     * @Assert\Type(type="integer")
     */
    public $id;
}