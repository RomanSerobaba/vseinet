<?php

namespace AdminBundle\Bus\Reserves\Query;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use AppBundle\Entity\BaseProduct;
use AppBundle\Doctrine\ORM\Query\DTORSM;
use AdminBundle\Bus\Reserves\Query\DTO\Supply;
use AppBundle\Enum\GoodsConditionCode;
use AppBundle\Enum\ProductAvailabilityCode;
use AppBundle\Enum\RepresentativeTypeCode;
use Doctrine\ORM\Query\ResultSetMapping;

class GetReservesQueryHandler extends MessageHandler
{
    public function handle(GetReservesQuery $query)
    {
        $em = $this->getDoctrine()->getManager();

        $product = $em->getRepository(BaseProduct::class)->find($query->baseProductId);

        if (!$product instanceof BaseProduct) {
            throw new NotFoundHttpException(sprintf('Товар с кодом %d не найден', $query->baseProductId));
        }

        $isFranchiser = RepresentativeTypeCode::FRANCHISER === $this->get('representative.identity')->getEmployeeRepresentative()->getType();
        $companyId = $this->get('representative.identity')->getEmployeeRepresentative()->getCompanyId();

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
                (si.purchase_price - si.bonus_amount - si.extra_discount_amount + si.charges) AS purchase_price,
                grrc.goods_condition_code,
                grrc.goods_pallet_id
            FROM goods_reserve_register_current AS grrc
            INNER JOIN supply_item AS si ON si.id = grrc.supply_item_id
            INNER JOIN any_doc AS a ON a.did = si.parent_did
            LEFT OUTER JOIN supply_doc AS sd ON sd.did = si.parent_did
            LEFT OUTER JOIN supplier AS s ON s.id = sd.supplier_id
            INNER JOIN base_product AS bp ON bp.id = grrc.base_product_id
            INNER JOIN geo_room AS gr ON gr.id = COALESCE(grrc.geo_room_id, grrc.destination_geo_room_id)
            INNER JOIN representative AS rep ON rep.geo_point_id = gr.geo_point_id
            WHERE bp.canonical_id = :base_product_id" . ($isFranchiser ? " AND rep.type = :type_code_FRANCHISER AND rep.company_id = :companyId" : "") . "
        ", new DTORSM(DTO\Reserve::class, DTORSM::ARRAY_INDEX));
        $q->setParameter('base_product_id', $product->getId());
        $q->setParameter('type_code_FRANCHISER', RepresentativeTypeCode::FRANCHISER);
        if ($isFranchiser) {
            $q->setParameter('companyId', $companyId);
        }
        $reserves = $q->getResult('DTOHydrator');

        if (!empty($reserves)) {

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
                    pp.is_handmade AS pricetag_is_handmade,
                    pps.handmade_price AS handmade_pricetag,
                    pp.printed_at AS pricetag_date,
                    CONCAT_WS(' ', p.lastname, p.firstname, p.secondname) AS pricetag_creator,
                    pps.handmade_created_at AS handmade_pricetag_date,
                    CONCAT_WS(' ', p2.lastname, p2.firstname, p2.secondname) AS handmade_pricetag_creator
                FROM geo_room AS gr
                INNER JOIN geo_point AS gp ON gp.id = gr.geo_point_id
                INNER JOIN geo_city AS gc ON gc.id = gp.geo_city_id
                LEFT OUTER JOIN product_pricetag AS pp ON pp.geo_point_id = gp.id AND pp.base_product_id = :base_product_id
                LEFT OUTER JOIN product_pricetag_settings AS pps ON pps.geo_point_id = gp.id AND pps.base_product_id = :base_product_id
                LEFT OUTER JOIN \"user\" AS u ON u.id = pp.printed_by
                LEFT OUTER JOIN person AS p ON p.id = u.person_id
                LEFT OUTER JOIN \"user\" AS u2 ON u2.id = pps.handmade_created_by
                LEFT OUTER JOIN person AS p2 ON p2.id = u2.person_id
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
                    $geoPoints[$geoObject->geoPointId] = new DTO\GeoPoint($geoObject->geoPointId, $geoObject->geoPointName, $geoObject->geoCityId, $geoObject->pricetag, $geoObject->pricetagIsHandmade, $geoObject->handmadePricetag, $geoObject->pricetagDate, $geoObject->pricetagCreator, $geoObject->handmadePricetagDate, $geoObject->handmadePricetagCreator);
                }

                $geoPoints[$geoObject->geoPointId]->geoRoomIds[$geoObject->geoRoomId] = $geoObject->geoRoomId;

                if (empty($geoRooms[$geoObject->geoRoomId])) {
                    $geoRooms[$geoObject->geoRoomId] = new DTO\GeoRoom($geoObject->geoRoomId, $geoObject->geoRoomName, $geoObject->geoPointId);
                }
            }

            $freeDelta =
            $freeReservedDelta =
            $freeAssembledDelta =
            $freeTransitDelta =
            $reservedDelta =
            $reservedAssembledDelta =
            $reservedTransitDelta =
            $issuedDelta =
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

                if ($reserve->destinationGeoRoomId) {
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
                        if ($reserve->goodsPalletId) {
                            $geoRooms[$geoRoomId]->supplies[$reserve->did]->freeAssembledDelta += $reserve->delta;
                            $geoRooms[$geoRoomId]->freeAssembledDelta += $reserve->delta;
                            $geoPoints[$geoPointId]->freeAssembledDelta += $reserve->delta;
                            $geoCities[$geoCityId]->freeAssembledDelta += $reserve->delta;
                            $freeAssembledDelta += $reserve->delta;
                        } elseif ($reserve->orderItemId) {
                            $geoRooms[$geoRoomId]->supplies[$reserve->did]->freeReservedDelta += $reserve->delta;
                            $geoRooms[$geoRoomId]->freeReservedDelta += $reserve->delta;
                            $geoPoints[$geoPointId]->freeReservedDelta += $reserve->delta;
                            $geoCities[$geoCityId]->freeReservedDelta += $reserve->delta;
                            $freeReservedDelta += $reserve->delta;
                        } else {
                            $geoRooms[$geoRoomId]->supplies[$reserve->did]->freeDelta += $reserve->delta;
                            $geoRooms[$geoRoomId]->freeDelta += $reserve->delta;
                            $geoPoints[$geoPointId]->freeDelta += $reserve->delta;
                            $geoCities[$geoCityId]->freeDelta += $reserve->delta;
                            $freeDelta += $reserve->delta;
                        }
                    } elseif (GoodsConditionCode::RESERVED === $reserve->goodsConditionCode) {
                        if ($reserve->goodsPalletId) {
                            $geoRooms[$geoRoomId]->supplies[$reserve->did]->reservedAssembledDelta += $reserve->delta;
                            $geoRooms[$geoRoomId]->reservedAssembledDelta += $reserve->delta;
                            $geoPoints[$geoPointId]->reservedAssembledDelta += $reserve->delta;
                            $geoCities[$geoCityId]->reservedAssembledDelta += $reserve->delta;
                            $reservedAssembledDelta += $reserve->delta;
                        } else {
                            $geoRooms[$geoRoomId]->supplies[$reserve->did]->reservedDelta += $reserve->delta;
                            $geoRooms[$geoRoomId]->reservedDelta += $reserve->delta;
                            $geoPoints[$geoPointId]->reservedDelta += $reserve->delta;
                            $geoCities[$geoCityId]->reservedDelta += $reserve->delta;
                            $reservedDelta += $reserve->delta;
                        }
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
            $reservesDTO->freeReservedDelta = $freeReservedDelta;
            $reservesDTO->freeAssembledDelta = $freeAssembledDelta;
            $reservesDTO->freeTransitDelta = $freeTransitDelta;
            $reservesDTO->reservedDelta = $reservedDelta;
            $reservesDTO->reservedAssembledDelta = $reservedAssembledDelta;
            $reservesDTO->reservedTransitDelta = $reservedTransitDelta;
            $reservesDTO->issuedDelta = $issuedDelta;
            $reservesDTO->issuedTransitDelta = $issuedTransitDelta;
        } else {
            $reservesDTO = new DTO\Reserves();
        }

        if ($isFranchiser) {
            $q = $em->createNativeQuery("
                with remains_data AS (
                    SELECT
                            SUM((si.purchase_price - si.bonus_amount - si.extra_discount_amount + si.charges) * grrc.delta) / SUM (grrc.delta) AS purchase_price
                    FROM goods_reserve_register_current AS grrc
                    INNER JOIN supply_item AS si ON si.id = grrc.supply_item_id
                    INNER JOIN base_product AS bp ON bp.id = grrc.base_product_id
                    INNER JOIN geo_room AS gr ON gr.id = COALESCE(grrc.geo_room_id, grrc.destination_geo_room_id)
                    INNER JOIN representative AS rep ON rep.geo_point_id = gr.geo_point_id
                    WHERE bp.canonical_id = :base_product_id  AND rep.type = :type_code_FRANCHISER AND grrc.goods_condition_code = :goodsConditionCode_FREE AND rep.company_id = :companyId
                )
                select rd.purchase_price as remains_purchase_price,
                    ceil(bp.pricelist_supplier_price * (1 + coalesce(wc2s.trade_margin, wc.default_margin) / 100) / 100) * 100 as supplier_purchase_price,
                    bp.supplier_availability_code as supplier_product_availability_code,
                    (select price from product where product_availability_code > :productAvailabilityCode_OUT_OF_STOCK and base_product_id = bp.id and geo_city_id = 0) as site_price
                from base_product AS bp
                    inner join company_to_financial_counteragent as c2fc on c2fc.company_id = :companyId
                    INNER JOIN wholesale_contract AS wc ON wc.financial_counteragent_id = c2fc.financial_counteragent_id
                    left outer join wholesale_contract_to_supplier as wc2s on wc2s.wholesale_contract_id = wc.id and wc2s.supplier_id = bp.supplier_id
                    inner join product as p on p.base_product_id = bp.id and p.geo_city_id = 0
                    left outer join remains_data as rd on 1 = 1
                where bp.id = :base_product_id and wc.terminated_at is null
            ", new ResultSetMapping());
            $q->setParameter('base_product_id', $product->getId());
            $q->setParameter('type_code_FRANCHISER', RepresentativeTypeCode::FRANCHISER);
            $q->setParameter('goodsConditionCode_FREE', GoodsConditionCode::FREE);
            $q->setParameter('productAvailabilityCode_AVAILABLE', ProductAvailabilityCode::AVAILABLE);
            $q->setParameter('productAvailabilityCode_ON_DEMAND', ProductAvailabilityCode::ON_DEMAND);
            $q->setParameter('productAvailabilityCode_IN_TRANSIT', ProductAvailabilityCode::IN_TRANSIT);
            $q->setParameter('productAvailabilityCode_OUT_OF_STOCK', ProductAvailabilityCode::OUT_OF_STOCK);
            $q->setParameter('companyId', $companyId);
            $a = $q->getResult('ListAssocHydrator')[0];
            $reservesDTO->remainsPurchasePrice = $a['remains_purchase_price'];
            $reservesDTO->supplierProductAvailabilityCode = $a['supplier_product_availability_code'];
            $reservesDTO->supplierPurchasePrice = $a['supplier_purchase_price'] > $a['site_price'] ? $a['site_price'] : $a['supplier_purchase_price'];
        }

        return $reservesDTO;
    }
}
