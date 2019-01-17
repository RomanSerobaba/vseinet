<?php

namespace AppBundle\Bus\Cart\Query;

use AppBundle\Bus\Message\MessageHandler;
use AppBundle\Entity\DiscountCode;
use AppBundle\Entity\Representative;
use AppBundle\Entity\GeoPoint;
use AppBundle\Enum\PaymentTypeCode;
use AppBundle\Enum\GoodsConditionCode;
use AppBundle\Entity\TransportCompany;
use AppBundle\Entity\PaymentType;use Doctrine\ORM\AbstractQuery;

class GetQueryHandler extends MessageHandler
{
    public function handle(GetQuery $query)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        $spec = "";
        $params = [];

        if (!empty($query->geoPointId)) {
            $geoPoint = $em->getRepository(GeoPoint::class)->find($geoPointId);

            if ($geoPoint instanceof GeoPoint && (empty($query->geoCityId) || $query->geoCityId == $geoPoint->getGeoCityId())) {
                $geoPointId = $geoPoint->getId();
                $geoCityId = $geoPoint->getGeoCityId();
            }
        }

        if (empty($geoPointId)) {
            if (!empty($query->geoCityId)) {
                $geoCityId = $query->geoCityId;
            } else {
                $geoCityId = $this->getGeoCity()->getId();
            }

            $q = $em->createQuery("
                SELECT r.geoPointId
                FROM AppBundle:Representative AS r
                JOIN AppBundle:GeoPoint AS gp WITH gp.id = r.geoPointId
                WHERE gp.geoCityId = :geoCityId AND r.isActive = TRUE AND r.hasRetail = TRUE
                ORDER BY r.isCentral
            ")
                ->setParameters([
                    'geoCityId' => $geoCityId,
                ])
                ->setMaxResults(1);
            $geoPointId = $q->getOneOrNullResult(AbstractQuery::HYDRATE_SINGLE_SCALAR);
        }

        $discount = $em->getRepository(DiscountCode::class)->findOneBy(['code' => $query->discountCode]);
        $stroikaCategoriesIds = [6654,6684,6699,7001,7492,7494,7496,7497,7501,7502,7507,7509,7569,7570,7571,7577,7578,7581,7582,7583,7584,7587,7588,7589,7590,7591,7593,7595,7596,7597,7598,7599,7600,7603,7606,7613,7615,7617,7618,7619,7623,7657,7658,7660,7697,13491,17999,5082851,5082367,34246,34478,34971,43238,43273,5078029,5078758,5078393,5078153,5078440,5088210,5078746,5078320,5078410,5078564,5078576,5078621,5078624,5079817,5081115,5081521,5081583,5081733,5083009,5084019,5084250,5085206,5085208,5085213,5085781];

        if ($discount instanceof DiscountCode) {
            $this->get('session')->set('discountCode', $query->discountCode);
        }

        if (null !== $user) {
            $q = $em->createQuery("
                SELECT
                    NEW AppBundle\Bus\Cart\Query\DTO\Product (
                        bp.id,
                        bp.name,
                        bp.categoryId,
                        bp.minQuantity,
                        bpi.basename,
                        COALESCE(p2.price, p.price),
                        COALESCE(p2.productAvailabilityCode, p.productAvailabilityCode),
                        COALESCE(p2.deliveryTax, p.deliveryTax),
                        c.quantity,
                        cp.id,
                        p.liftingTax,
                        p.discountAmount,
                        (
                            SELECT
                                SUM(grrc.delta)
                            FROM AppBundle:GoodsReserveRegisterCurrent AS grrc
                            JOIN AppBundle:GeoRoom AS gr WITH gr.id = grrc.geoRoomId
                            WHERE grrc.baseProductId = bp.id AND gr.geoPointId = :geoPointId AND grrc.goodsConditionCode = :goodsConditionCode_FREE AND grrc.goodsPalletId IS NULL AND grrc.orderItemId IS NULL
                        ),
                        (
                            SELECT
                                pp.price
                            FROM AppBundle:ProductPricetag AS pp
                            WHERE pp.baseProductId = bp.id AND pp.geoPointId = :geoPointId
                        )
                    )
                FROM AppBundle:Cart c
                INNER JOIN AppBundle:BaseProduct AS bp WITH bp.id = c.baseProductId
                LEFT OUTER JOIN AppBundle:BaseProductImage AS bpi WITH bpi.baseProductId = bp.id AND bpi.sortOrder = 1
                LEFT OUTER JOIN AppBundle:Product AS p2 WITH p2.baseProductId = bp.id AND p2.geoCityId = :geoCityId
                INNER JOIN AppBundle:Product AS p WITH p.baseProductId = bp.id AND p.geoCityId = 0
                LEFT JOIN AppBundle:CategoryPath AS cp WITH cp.id = bp.categoryId AND cp.pid IN (:stroikaCategoriesIds)
                WHERE c.userId = :userId
            ");
            $q->setParameters([
                'userId' => $user->getId(),
                'geoCityId' => $geoCityId,
                'geoPointId' => $geoPointId,
                'stroikaCategoriesIds' => $stroikaCategoriesIds,
                'goodsConditionCode_FREE' => GoodsConditionCode::FREE,
            ]);
            $products = $q->getResult('IndexByHydrator');
        }
        else {
            $products = $this->get('session')->get('cart', []);

            if (!empty($products)) {
                $q = $em->createQuery("
                    SELECT
                        NEW AppBundle\Bus\Cart\Query\DTO\Product (
                            bp.id,
                            bp.name,
                            bp.categoryId,
                            bp.minQuantity,
                            bpi.basename,
                            COALESCE(p2.price, p.price),
                            COALESCE(p2.productAvailabilityCode, p.productAvailabilityCode),
                            COALESCE(p2.deliveryTax, p.deliveryTax),
                            0,
                            cp.id,
                            p.liftingTax,
                            p.discountAmount,
                            (
                                SELECT
                                    SUM(grrc.delta)
                                FROM AppBundle:GoodsReserveRegisterCurrent AS grrc
                                JOIN AppBundle:GeoRoom AS gr WITH gr.id = grrc.geoRoomId
                                WHERE grrc.baseProductId = bp.id AND gr.geoPointId = :geoPointId AND grrc.goodsConditionCode = :goodsConditionCode_FREE AND grrc.goodsPalletId IS NULL AND grrc.orderItemId IS NULL
                            ),
                            (
                                SELECT
                                    pp.price
                                FROM AppBundle:ProductPricetag AS pp
                                WHERE pp.baseProductId = bp.id AND pp.geoPointId = :geoPointId
                            )
                        )
                    FROM AppBundle:BaseProduct AS bp
                    LEFT OUTER JOIN AppBundle:BaseProductImage AS bpi WITH bpi.baseProductId = bp.id AND bpi.sortOrder = 1
                    LEFT OUTER JOIN AppBundle:Product AS p2 WITH p2.baseProductId = bp.id AND p2.geoCityId = :geoCityId
                    INNER JOIN AppBundle:Product AS p WITH p.baseProductId = bp.id AND p.geoCityId = 0
                    LEFT JOIN AppBundle:CategoryPath AS cp WITH cp.id = bp.categoryId AND cp.pid IN (:stroikaCategoriesIds)
                    WHERE bp.id IN (:ids)
                ");
                $q->setParameters([
                    'ids' => array_keys($products),
                    'geoCityId' => $geoCityId,
                    'geoPointId' => $geoPointId,
                    'stroikaCategoriesIds' => $stroikaCategoriesIds,
                    'goodsConditionCode_FREE' => GoodsConditionCode::FREE,
                ]);
                foreach ($q->getResult() as $product) {
                    $product->quantity = intval($products[$product->id]['quantity']);
                    $products[$product->id] = $product;
                }
            }
        }

        return new DTO\Cart(array_values($products), $discount instanceof DiscountCode ? $discount->getCode() : null, $geoPointId);
    }
}
