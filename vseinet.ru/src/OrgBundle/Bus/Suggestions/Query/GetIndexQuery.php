<?php 

namespace OrgBundle\Bus\Suggestions\Query;

use AppBundle\Bus\Message\Message;
use AppBundle\Annotation as VIA;
use Symfony\Component\Validator\Constraints as Assert;

class GetIndexQuery extends Message
{
    /**
     * @VIA\Description("Complaint lastId (для пагинации)")
     * @Assert\Type(type="integer")
     */
    public $lastId;

    /**
     * @VIA\Description("Limit (для пагинации)")
     * @Assert\Type(type="integer")
     * @VIA\DefaultValue(30)
     */
    public $limit;

    /**
     * @VIA\Description("Показать все предложения")
     * @Assert\Type(type="boolean")
     * @VIA\DefaultValue("false")
     */
    public $isAll;
}