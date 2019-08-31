<?php

namespace AdminBundle\Bus\Reserves\Query;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use AppBundle\Entity\BaseProduct;
use AppBundle\Enum\ProductAvailabilityCode;
use AppBundle\Doctrine\ORM\Query\DTORSM;
use AdminBundle\Bus\Reserves\Query\DTO\Supply;
use AppBundle\Enum\GoodsConditionCode;

class GetReservesQueryHandler extends MessageHandler
{
    public function handle(GetReservesQuery $query)
    {
        $em = $this->getDoctrine()->getManager();

        $product = $em->getRepository(BaseProduct::class)->find($query->baseProductId);

        if (!$product instanceof BaseProduct) {
            throw new NotFoundHttpException(sprintf('Товар с кодом %d не найден', $query->baseProductId));
        }

        $q = $em->createNativeQuery("
            SELECT
                si.parent_did AS did,
                grrc.geo_room_id,
                grrc.order_item_id,
                grrc.destination_geo_room_id,
                COALESCE(s.code, 'VS') AS code,
                a.number,
                a.created_at,
                grrc.delta,
                (si.purchase_price - si.bonus_amount) AS purchase_price,
                grrc.goods_condition_code,
                grrc.goods_pallet_id
            FROM goods_reserve_register_current AS grrc
            INNER JOIN supply_item AS si ON si.id = grrc.supply_item_id
            INNER JOIN any_doc AS a ON a.did = si.parent_did
            LEFT OUTER JOIN supply_doc AS sd ON sd.did = si.parent_did
            LEFT OUTER JOIN supplier AS s ON s.id = sd.supplier_id
            WHERE grrc.base_product_id = :base_product_id
        ", new DTORSM(DTO\Reserve::class, DTORSM::ARRAY_INDEX));
        $q->setParameter('base_product_id', $product->getId());
        $reserves = $q->getResult('DTOHydrator');

        if (empty($reserves)) {
            return new DTO\Reserves();
        }

        $roomIds = [];

        foreach ($reserves as $reserve) {
            $geoRoomId = $reserve->geoRoomId ? $reserve->geoRoomId : $reserve->destinationGeoRoomId;
            $roomIds[$geoRoomId] = $geoRoomId;
        }

        $q = $em->createNativeQuery("
            SELECT
                gr.id AS geo_room_id,
                gr.name AS geo_room_name,
                gp.id AS geo_point_id,
                gp.name AS geo_point_name,
                gc.id AS geo_city_id,
                gc.name AS geo_city_name,
                pp.price AS pricetag,
                pp.is_handmade AS pricetag_is_handmade
            FROM geo_room AS gr
            INNER JOIN geo_point AS gp ON gp.id = gr.geo_point_id
            INNER JOIN geo_city AS gc ON gc.id = gp.geo_city_id
            LEFT OUTER JOIN product_pricetag AS pp ON pp.geo_point_id = gp.id AND pp.base_product_id = :base_product_id
            WHERE gr.id IN (:ids)
        ", new DTORSM(DTO\GeoObject::class));
        $q->setParameter('ids', $roomIds);
        $q->setParameter('base_product_id', $product->getId());
        $geoObjects = $q->getResult('DTOHydrator');

        $geoCities = [];
        $geoPoints = [];
        $geoRooms = [];

        foreach ($geoObjects as $geoObject) {
            if (empty($geoCities[$geoObject->geoCityId])) {
                $geoCities[$geoObject->geoCityId] = new DTO\GeoCity($geoObject->geoCityId, $geoObject->geoCityName);
            }

            $geoCities[$geoObject->geoCityId]->geoPointIds[$geoObject->geoPointId] = $geoObject->geoPointId;

            if (empty($geoPoints[$geoObject->geoPointId])) {
                $geoPoints[$geoObject->geoPointId] = new DTO\GeoPoint($geoObject->geoPointId, $geoObject->geoPointName, $geoObject->geoCityId, $geoObject->pricetag, $geoObject->pricetagIsHandmade);
            }

            $geoPoints[$geoObject->geoPointId]->geoRoomIds[$geoObject->geoRoomId] = $geoObject->geoRoomId;

            if (empty($geoRooms[$geoObject->geoRoomId])) {
                $geoRooms[$geoObject->geoRoomId] = new DTO\GeoRoom($geoObject->geoRoomId, $geoObject->geoRoomName, $geoObject->geoPointId);
            }
        }

        $freeDelta = 0;
        $freeTransitDelta = 0;
        $reservedDelta = 0;
        $reservedTransitDelta = 0;
        $issuedDelta = 0;
        $issuedTransitDelta = 0;

        foreach ($reserves as $reserve) {
            $geoRoomId = $reserve->geoRoomId ? $reserve->geoRoomId : $reserve->destinationGeoRoomId;
            $geoPointId = $geoRooms[$geoRoomId]->geoPointId;
            $geoCityId = $geoPoints[$geoPointId]->geoCityId;

            if (!isset($geoRooms[$geoRoomId]->supplies[$reserve->did])) {
                $geoRooms[$geoRoomId]->supplies[$reserve->did] = new Supply(
                    $reserve->did,
                    $reserve->number,
                    $reserve->code,
                    $reserve->createdAt,
                    $reserve->purchasePrice
                );
            }

            if ($reserve->destinationGeoRoomId || $reserve->goodsPalletId) {
                if (GoodsConditionCode::FREE === $reserve->goodsConditionCode) {
                    $geoRooms[$geoRoomId]->supplies[$reserve->did]->freeTransitDelta += $reserve->delta;
                    $geoRooms[$geoRoomId]->freeTransitDelta += $reserve->delta;
                    $geoPoints[$geoPointId]->freeTransitDelta += $reserve->delta;
                    $geoCities[$geoCityId]->freeTransitDelta += $reserve->delta;
                    $freeTransitDelta += $reserve->delta;
                } elseif (GoodsConditionCode::RESERVED === $reserve->goodsConditionCode) {
                    $geoRooms[$geoRoomId]->supplies[$reserve->did]->reservedTransitDelta += $reserve->delta;
                    $geoRooms[$geoRoomId]->reservedTransitDelta += $reserve->delta;
                    $geoPoints[$geoPointId]->reservedTransitDelta += $reserve->delta;
                    $geoCities[$geoCityId]->reservedTransitDelta += $reserve->delta;
                    $reservedTransitDelta += $reserve->delta;
                } else {
                    $geoRooms[$geoRoomId]->supplies[$reserve->did]->issuedTransitDelta += $reserve->delta;
                    $geoRooms[$geoRoomId]->issuedTransitDelta += $reserve->delta;
                    $geoPoints[$geoPointId]->issuedTransitDelta += $reserve->delta;
                    $geoCities[$geoCityId]->issuedTransitDelta += $reserve->delta;
                    $issuedTransitDelta += $reserve->delta;
                }
            } else {
                if (GoodsConditionCode::FREE === $reserve->goodsConditionCode) {
                    $geoRooms[$geoRoomId]->supplies[$reserve->did]->freeDelta += $reserve->delta;
                    $geoRooms[$geoRoomId]->freeDelta += $reserve->delta;
                    $geoPoints[$geoPointId]->freeDelta += $reserve->delta;
                    $geoCities[$geoCityId]->freeDelta += $reserve->delta;
                    $freeDelta += $reserve->delta;
                } elseif (GoodsConditionCode::RESERVED === $reserve->goodsConditionCode) {
                    $geoRooms[$geoRoomId]->supplies[$reserve->did]->reservedDelta += $reserve->delta;
                    $geoRooms[$geoRoomId]->reservedDelta += $reserve->delta;
                    $geoPoints[$geoPointId]->reservedDelta += $reserve->delta;
                    $geoCities[$geoCityId]->reservedDelta += $reserve->delta;
                    $reservedDelta += $reserve->delta;
                } else {
                    $geoRooms[$geoRoomId]->supplies[$reserve->did]->issuedDelta += $reserve->delta;
                    $geoRooms[$geoRoomId]->issuedDelta += $reserve->delta;
                    $geoPoints[$geoPointId]->issuedDelta += $reserve->delta;
                    $geoCities[$geoCityId]->issuedDelta += $reserve->delta;
                    $issuedDelta += $reserve->delta;
                }
            }
        }

        $reservesDTO = new DTO\Reserves($geoCities, $geoPoints, $geoRooms);
        $reservesDTO->freeDelta = $freeDelta;
        $reservesDTO->freeTransitDelta = $freeTransitDelta;
        $reservesDTO->reservedDelta = $reservedDelta;
        $reservesDTO->reservedTransitDelta = $reservedTransitDelta;
        $reservesDTO->issuedDelta = $issuedDelta;
        $reservesDTO->issuedTransitDelta = $issuedTransitDelta;

        return $reservesDTO;
    }
}
