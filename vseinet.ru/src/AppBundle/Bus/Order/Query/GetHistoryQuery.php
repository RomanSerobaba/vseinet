<?php 

namespace AppBundle\Bus\Order\Query;

use AppBundle\Bus\Message\Message;
use Symfony\Component\Validator\Constraints as Assert;

class GetHistoryQuery extends Message
{
    /**
     * @Assert\Choice({"list", "table", "light"}, strict=true)
     */
    public $mode = 'list';

    /**
     * @Assert\Type(type="integer")
     */
    public $page = 1;

    /**
     * @Assert\Type(type="integer")
     */
    public $limit = 25;
}
