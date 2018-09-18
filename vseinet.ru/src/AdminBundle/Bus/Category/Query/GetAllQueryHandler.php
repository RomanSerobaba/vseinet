<?php

namespace AdminBundle\Bus\Category\Query;

use AppBundle\Bus\Message\MessageHandler;

class GetAllQueryHandler extends MessageHandler
{
    public function handle(GetAllQuery $query)
    {
        $query = $this->getDoctrine()->getManager()->createQuery("
            SELECT 
                NEW AdminBundle\Bus\Category\Query\DTO\Category (
                    c.id,
                    c.name,
                    c.pid,
                    cp.level
                )
            FROM AppBundle:Category AS c 
            INNER JOIN AppBundle:CategoryPath AS cp WITH cp.id = c.id AND cp.id = cp.pid 
            ORDER BY cp.level, c.name
        ");

        return $query->getResult();
    }
}
