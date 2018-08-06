<?php

namespace SiteBundle\Bus\Search\Query;

use AppBundle\Bus\Message\MessageHandler;

class GetCounterQueryHandler extends MessageHandler
{
    public function handle(GetCounterQuery $query)
    {
        $q = $this->getDoctrine()->getManager()->createQuery("
            SELECT c.countProducts
            FROM ContentBundle:Category c 
            WHERE c.id = 0
        ");
        $counter = $q->getSingleScalarResult() ?: 12345678;

        return $counter;
    }
}