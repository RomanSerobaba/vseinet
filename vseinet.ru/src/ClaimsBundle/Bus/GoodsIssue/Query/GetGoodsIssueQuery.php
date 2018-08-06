<?php 

namespace ClaimsBundle\Bus\GoodsIssue\Query;

use AppBundle\Bus\Message\Message;
use AppBundle\Annotation as VIA;
use Symfony\Component\Validator\Constraints as Assert;

class GetGoodsIssueQuery extends Message
{
    /**
     * @VIA\Description("Goods issue id")
     */
    public $id;
}