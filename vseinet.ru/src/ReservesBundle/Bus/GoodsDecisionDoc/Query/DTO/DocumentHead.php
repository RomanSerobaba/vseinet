<?php 

namespace ReservesBundle\Bus\GoodsDecisionDoc\Query\DTO;

use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Annotation as VIA;

class DocumentHead extends DocumentList
{
    use \DocumentBundle\Prototipe\DocumentDTO;
}