<?php

namespace AppBundle\Bus\Pricetags\Query;

use AppBundle\Bus\Message\MessageHandler;

class GetQueryHandler extends MessageHandler
{
    public function handle(GetQuery $query)
    {
        $q = $this->getDoctrine()->getManager()->createQuery('
            SELECT
                NEW AppBundle\Bus\Pricetags\Query\DTO\Pricetag (
                    pt.baseProductId,
                    pt.geoPointId,
                    pt.price,
                    pt.isHandmade,
                    pt.size,
                    pt.color
                )
            FROM AppBundle:Pricetag AS pt
            WHERE pt.baseProductId = :baseProductId AND pt.geoPointId = :geoPointId
        ');
        $q->setParameter('baseProductId', $query->baseProductId);
        $q->setParameter('geoPointId', $query->geoPointId);
        $pricetags = $q->getArrayResult();

        return empty($pricetags) ? null : $pricetags[0];
    }
}
