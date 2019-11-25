<?php

namespace AppBundle\Bus\Order\Query;

use AppBundle\Bus\Message\MessageHandler;

class SearchCounteragentQueryHandler extends MessageHandler
{
    public function handle(SearchCounteragentQuery $query)
    {
        $q = $this->getDoctrine()->getManager()->createQuery("
            SELECT
                NEW AppBundle\Bus\Order\Query\DTO\Counteragent (
                    c.kpp,
                    c.name,
                    ga.address,
                    sa.number,
                    b.bic,
                    b.name,
                    b.id,
                    c.tin
                )
            FROM AppBundle:Counteragent AS c
            LEFT JOIN AppBundle:GeoAddress AS ga WITH ga.id = c.legalAddressId
            LEFT JOIN AppBundle:CounteragentSettlementAccount AS sa WITH sa.counteragentId = c.id
            LEFT JOIN AppBundle:Bank AS b WITH b.id = sa.bankId
            WHERE LOWER(c.name) LIKE LOWER(:name)
            ORDER BY c.name
        ");
        $q->setParameter('name', '%'.$query->q.'%');
        $q->setMaxResults($query->limit);

        return $q->getResult();
    }
}
