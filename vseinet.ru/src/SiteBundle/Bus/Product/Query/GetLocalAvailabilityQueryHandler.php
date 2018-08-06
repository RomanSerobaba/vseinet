<?php 

namespace SiteBundle\Bus\Product\Query;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use ContentBundle\Entity\BaseProduct;
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

        // if ($this->get('user.identity')->isClient()) {
        //     // для клиентов наличие только по текущему городу
        //     $criteria = " AND gp.geoCityId = ".$this->get('city.identity')->getId();   
        // } else {
            $criteria = "";
        // }

        $q = $em->createQuery("
            SELECT 
                NEW SiteBundle\Bus\Product\Query\DTO\Point (
                    gp.id,
                    gp.code,
                    gp.name,
                    SUM(grrc.delta)
                )
            FROM RegisterBundle:GoodsReserveRegisterCurrent AS grrc
            INNER JOIN GeoBundle:GeoRoom AS gr WITH gr.id = grrc.geoRoomId
            INNER JOIN GeoBundle:GeoPoint AS gp WITH gp.id = gr.geoPointId 
            WHERE grrc.baseProductId = :baseProductId AND grrc.goodsConditionCode = :conditionCode {$criteria}
            GROUP BY gp.id
            ORDER BY gp.geoCityId, gp.name
        ");
        $q->setParameter('baseProductId', $baseProduct->getId());
        $q->setParameter('conditionCode', GoodsConditionCode::FREE);
        $points = $q->getArrayResult();

        return $points;
    }
}
