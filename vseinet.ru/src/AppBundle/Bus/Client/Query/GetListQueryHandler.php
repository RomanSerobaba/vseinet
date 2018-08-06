<?php 

namespace AppBundle\Bus\Client\Query;

use AppBundle\Bus\Message\MessageHandler;

class GetListQueryHandler extends MessageHandler
{
    public function handle(GetListQuery $query)
    {
        $q = $this->getDoctrine()->getManager()->createQuery("
            SELECT 
                NEW AppBundle\Bus\Client\Query\DTO\Client (
                    c.id,
                    c.name,
                    c.redirectUris
                )
            FROM AppBundle:Client c 
            ORDER BY c.id 
        ");

        return $q->getArrayResult();
    }
}