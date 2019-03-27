<?php

namespace AppBundle\Bus\Catalog\Query;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class GetDetailValueQueryHandler extends MessageHandler
{
    public function handle(GetDetailValueQuery $query)
    {
        $q = $this->getDoctrine()->getManager()->createQuery("
            SELECT
                NEW AppBundle\Bus\Catalog\Query\DTO\DetailValue (
                    dv.id,
                    dv.value,
                    dv.detailId,
                    d.name
                )
            FROM AppBundle:DetailToProduct AS d2p
            INNER JOIN AppBundle:Detail AS d WITH d.id = d2p.detailId
            INNER JOIN AppBundle:DetailValue AS dv WITH dv.id = d2p.valueId
            WHERE dv.id = :id
            GROUP BY d.id, dv.id
        ");
        $q->setParameter('id', $query->id);
        $value = $q->getOneOrNullResult();

        if (!$value instanceof DTO\DetailValue) {
            throw new NotFoundHttpException();
        }

        return $value;
    }
}
