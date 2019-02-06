<?php

namespace AppBundle\Bus\Product\Query;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use AppBundle\Entity\BaseProduct;
use AppBundle\Enum\GoodsConditionCode;

class GetLocalAvailabilityQueryHandler extends MessageHandler
{
    public function handle(GetLocalAvailabilityQuery $query)
    {
        $em = $this->getDoctrine()->getManager();

        $baseProduct = $em->getRepository(BaseProduct::class)->find($query->baseProductId);
        if (!$baseProduct instanceof BaseProduct){
            throw new NotFoundHttpException();
        }

        if (!$this->getUserIsEmployee()) {
            // для клиентов наличие только по текущему городу
            $criteria = " AND gp.geoCityId = ".$this->getGeoCity()->getRealId();
        } else {
            $criteria = "";
        }

        $q = $em->createQuery("
            SELECT
                NEW AppBundle\Bus\Product\Query\DTO\GeoPoint (
                    gp.id,
                    gp.code,
                    gp.name,
                    SUM(grrc.delta)
                )
            FROM AppBundle:GoodsReserveRegisterCurrent AS grrc
            INNER JOIN AppBundle:GeoRoom AS gr WITH gr.id = grrc.geoRoomId
            INNER JOIN AppBundle:GeoPoint AS gp WITH gp.id = gr.geoPointId
            WHERE grrc.baseProductId = :baseProductId AND grrc.goodsConditionCode = :conditionCode AND grrc.orderItemId IS NULL {$criteria}
            GROUP BY gp.id
            ORDER BY gp.geoCityId, gp.name
        ");
        $q->setParameter('baseProductId', $baseProduct->getId());
        $q->setParameter('conditionCode', GoodsConditionCode::FREE);
        $geoPoints = $q->getArrayResult();

        return $geoPoints;
    }
}
