<?php 

namespace ShopBundle\Bus\Cart\Command;

use AppBundle\Bus\Message\Message;
use AppBundle\Annotation as VIA;
use Symfony\Component\Validator\Constraints as Assert;

class ClearCommand extends Message
{
}