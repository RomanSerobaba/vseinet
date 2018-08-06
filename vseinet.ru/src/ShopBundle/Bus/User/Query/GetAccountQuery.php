<?php 

namespace ShopBundle\Bus\User\Query;

use AppBundle\Bus\Message\Message;
use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Annotation as VIA;

class GetAccountQuery extends Message
{
}