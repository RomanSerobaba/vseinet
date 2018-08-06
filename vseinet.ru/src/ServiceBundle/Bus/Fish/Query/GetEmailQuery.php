<?php 

namespace ServiceBundle\Bus\Fish\Query;

use AppBundle\Bus\Message\Message;
use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Annotation as VIA;

class GetEmailQuery extends Message
{
}