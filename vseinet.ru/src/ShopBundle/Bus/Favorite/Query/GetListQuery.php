<?php 

namespace ShopBundle\Bus\Favorite\Query;

use AppBundle\Bus\Message\Message;
use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Annotation as VIA;

class GetListQuery extends Message
{
}