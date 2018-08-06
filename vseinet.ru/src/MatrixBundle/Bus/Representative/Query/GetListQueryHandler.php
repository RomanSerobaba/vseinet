<?php 

namespace MatrixBundle\Bus\Representative\Query;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class GetListQueryHandler extends MessageHandler
{
    public function handle(GetListQuery $query)
    {
        $q = $this->getDoctrine()->getManager()->createQuery("
            SELECT 
                NEW MatrixBundle\Bus\Representative\Query\DTO\Representative(
                    r.geoPointId,
                    gp.name,
                    ml.limitAmount,
                    COALESCE(SUM(grr.delta * si.purchasePrice), 0),
                    (SELECT CONCAT('[', GROUP_CONCAT(mtr.tradeMatrixTemplateId), ']') FROM MatrixBundle:TradeMatrixTemplateToRepresentative AS mtr WHERE mtr.representativeId = r.geoPointId)
                )
            FROM OrgBundle:Representative AS r
            JOIN GeoBundle:GeoPoint AS gp WITH gp.id = r.geoPointId
            JOIN MatrixBundle:TradeMatrixLimit AS ml WITH ml.representativeId = r.geoPointId
            JOIN GeoBundle:GeoRoom AS gr WITH gr.geoPointId = gp.id
            LEFT JOIN ReservesBundle:GoodsReserveRegisterCurrent AS grr WITH grr.geoRoomId = gr.id AND grr.delta > 0
            LEFT JOIN SupplyBundle:SupplyItem AS si WITH si.id = grr.supplyItemId
            GROUP BY r.geoPointId, gp.name, ml.limitAmount
        ");

        return $q->getArrayResult();
    }
}