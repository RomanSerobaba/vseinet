<?php

namespace AppBundle\Bus\Product\Query;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use AppBundle\Entity\BaseProduct;
use AppBundle\Doctrine\ORM\Query\DTORSM;

class GetLocalAvailabilityQueryHandler extends MessageHandler
{
    public function handle(GetLocalAvailabilityQuery $query)
    {
        $em = $this->getDoctrine()->getManager();

        $baseProduct = $em->getRepository(BaseProduct::class)->find($query->baseProductId);
        if (!$baseProduct instanceof BaseProduct) {
            throw new NotFoundHttpException(sprintf('Товар с кодом %d не найден', $query->baseProductId));
        }

        $q = $em->createNativeQuery("
            SELECT
                gp.id,
                gp.geo_city_id,
                gp.code,
                gp.name,
                SUM(grrc.delta) AS quantity,
                ga.address
            FROM goods_reserve_register_current AS grrc
            INNER JOIN geo_room AS gr ON gr.id = grrc.geo_room_id
            INNER JOIN geo_point AS gp ON gp.id = gr.geo_point_id
            LEFT OUTER JOIN geo_address AS ga ON ga.id = gp.geo_address_id
            WHERE grrc.base_product_id = :base_product_id
                AND grrc.goods_condition_code = 'free'::goods_condition_code
                AND grrc.order_item_id IS NULL
            GROUP BY gp.id, ga.address
            HAVING SUM(grrc.delta) > 0
            ORDER BY gp.geo_city_id, gp.name
        ", new DTORSM(DTO\GeoPoint::class));
        $q->setParameter('base_product_id', $baseProduct->getId());
        $geoPoints = $q->getResult('DTOHydrator');

        return $geoPoints;
    }
}
