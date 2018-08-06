<?php 

namespace SupplyBundle\Bus\LowCostPurchases\Query;

use AppBundle\Bus\Message\Message;
use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Annotation as VIA;

class GetCategoriesQuery extends Message
{
}