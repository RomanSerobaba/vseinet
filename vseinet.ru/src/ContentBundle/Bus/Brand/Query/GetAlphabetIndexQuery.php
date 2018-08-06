<?php 

namespace ContentBundle\Bus\Brand\Query;

use AppBundle\Bus\Message\Message;
use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Annotation as VIA;

class GetAlphabetIndexQuery extends Message
{
    /**
     * @VIA\Description("Фильтр брендов")
     * @Assert\Choice({"all", "active", "forbidden"}, strict=true)
     * @VIA\DefaultValue("active")
     */
    public $filter;
}