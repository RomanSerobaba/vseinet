<?php 

namespace CatalogBundle\Bus\Categories\Query;

use AppBundle\Bus\Message\Message;
use AppBundle\Annotation as VIA;
use Symfony\Component\Validator\Constraints as Assert;

class GetEmployeesFilterQuery extends Message
{
    /**
     * @Assert\Type(type="integer")
     * @VIA\Description("Конкурент")
     * @Assert\NotBlank
     */
    public $competitorId;

    /**
     * @Assert\Type(type="integer")
     * @VIA\Description("Город")
     */
    public $cityId;
}