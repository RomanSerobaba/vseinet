<?php 

namespace AdminBundle\Bus\Reserves\Query;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use AppBundle\Entity\BaseProduct;
use AppBundle\Enum\ProductAvailabilityCode;
use AppBundle\Doctrine\ORM\Query\DTORSM;

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
                sd.did,
                grrc.geo_room_id,
                grrc.destination_geo_room_id,
                s.code,
                sd.number,
                sd.created_at,
                grrc.delta,
                si.purchase_price,
                grrc.goods_condition_code
            FROM goods_reserve_register_current AS grrc 
            INNER JOIN supply_item AS si ON si.id = grrc.supply_item_id 
            INNER JOIN supply_doc AS sd ON sd.did = si.parent_did
            INNER JOIN supplier AS s ON s.id = sd.supplier_id 
            WHERE grrc.base_product_id = :base_product_id
        ", new DTORSM(DTO\Supply::class, DTORSM::ARRAY_ASSOC));
        $q->setParameter('base_product_id', $product->getId());
        $supplies = $q->getResult('DTOHydrator');

        if (empty($supplies)) {
            return new DTO\Reserves();
        }

        $roomIds = [];
        foreach ($supplies as $supply) {
            if (null !== $supply->geoRoomId) {
                $roomIds[$supply->geoRoomId] = $supply->geoRoomId;
            }
            if (null !== $supply->destinationGeoRoomId) {
                $roomIds[$supply->destinationGeoRoomId] = $supply->destinationGeoRoomId;
            }
        }

        $q = $em->createNativeQuery("
            SELECT 
                gr.id AS geo_room_id,
                gr.name AS geo_room_name,
                gp.id AS geo_point_id,
                gp.name AS geo_point_name,
                gc.id AS geo_city_id,
                gc.name AS geo_city_name
            FROM geo_room AS gr 
            INNER JOIN geo_point AS gp ON gp.id = gr.geo_point_id 
            INNER JOIN geo_city AS gc ON gc.id = gp.geo_city_id 
            WHERE gr.id IN (:ids)
        ", new DTORSM(DTO\GeoObject::class));
        $q->setParameter('ids', $roomIds);
        $geoObjects = $q->getResult('DTOHydrator');

        $geoCities = [];
        $geoPoints = [];
        $geoRooms = [];
        foreach ($geoObjects as $geoObject) {
            if (empty($geoCities[$geoObject->geoCityId])) {
                $geoCities[$geoObject->geoCityId] = new DTO\GeoCity($geoObject->geoCityId, $geoObject->geoCityName);
            }
            $geoCities[$geoObject->geoCityId]->geoPointIds[$geoObject->geoPointId] = $geoObject->geoPointId;
            if (empty($geoPOints[$geoObject->geoPointId])) {
                $geoPoints[$geoObject->geoPointId] = new DTO\GeoPoint($geoObject->geoPointId, $geoObject->geoPointName);
            }
            $geoPoints[$geoObject->geoPointId]->geoRoomIds[$geoObject->geoRoomId] = $geoObject->geoRoomId;
            if (empty($geoRooms[$geoObject->geoRoomId])) {
                $geoRooms[$geoObject->geoRoomId] = new DTO\GeoRoom($geoObject->geoRoomId, $geoObject->geoRoomName);
            }
        }

        foreach ($supplies AS $supply) {
            if (null !== $supply->destinationGeoRoomId) {
                $geoRooms[$supply->destinationGeoRoomId]->supplyIds[] = $supply->did;
            }
            elseif (null !== $supply->geoRoomId) {
                $geoRooms[$supply->geoRoomId]->supplyIds[] = $supply->did;
            }
        }

        return new DTO\Reserves($geoCities, $geoPoints, $geoRooms, $supplies);
    }
}
