<?php 

namespace ContentBundle\Bus\ParserSource\Query;

use AppBundle\Bus\Message\Message;
use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Annotation as VIA;

class GetListQuery extends Message
{
    /**
     * @Assert\Choice({"all", "active"}, strict=true)
     * @VIA\Description("Фильтр истоников парсинга")
     * @VIA\DefaultValue("all")
     */
    public $filter;
}