<?php

namespace AppBundle\Bus\Search\Query;

use AppBundle\Bus\Message\MessageHandler;

class GetCounterQueryHandler extends MessageHandler
{
    public function handle(GetCounterQuery $query)
    {
        $q = $this->getDoctrine()->getManager()->createQuery("
            SELECT c.countProducts
            FROM AppBundle:Category c 
            WHERE c.id = 0
        ");
        try {
            return $q->getSingleScalarResult();
        } catch (\Exception $e) {
        }

        return 0;
    }
}
