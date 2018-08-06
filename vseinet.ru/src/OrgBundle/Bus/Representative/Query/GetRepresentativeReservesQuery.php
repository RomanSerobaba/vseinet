<?php 

namespace OrgBundle\Bus\Representative\Query;

use AppBundle\Bus\Message\Message;
use AppBundle\Annotation as VIA;
use Symfony\Component\Validator\Constraints as Assert;

class GetRepresentativeReservesQuery extends Message
{
}