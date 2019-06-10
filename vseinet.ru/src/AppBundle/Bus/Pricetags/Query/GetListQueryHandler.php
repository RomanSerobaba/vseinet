<?php

namespace AppBundle\Bus\Pricetags\Query;

use AppBundle\Bus\Message\MessageHandler;

class GetListQueryHandler extends MessageHandler
{
    public function handle(GetListQuery $query)
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
            WHERE pt.baseProductId IN(:baseProductIds) AND pt.geoPointId = :geoPointId
        ');
        $q->setParameter('baseProductIds', $query->baseProductIds);
        $q->setParameter('geoPointId', $query->geoPointId);
        $pricetags = $q->getResult('IndexByHydrator');

        return $pricetags;
    }
}
