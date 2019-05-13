<?php

namespace AppBundle\Bus\Product\Query;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use AppBundle\Doctrine\ORM\Query\DTORSM;
use AppBundle\Entity\BaseProduct;
use AppBundle\Entity\Supplier;
use AppBundle\Enum\ProductAvailabilityCode;
use Cron\CronExpression;

class GetDeliveryDateQueryHandler extends MessageHandler
{
    /**
     * @var CronExpression
     */
    protected $cron;

    /**
     * @var array
     */
    protected $routes;


    public function handle(GetDeliveryDateQuery $query)
    {
        $em = $this->getDoctrine()->getManager();

        $baseProduct = $em->getRepository(BaseProduct::class)->find($query->baseProductId);
        if (!$baseProduct instanceof BaseProduct) {
            throw new NotFoundHttpException(sprintf('Товар с кодом %d не найден', $query->baseProductId));
        }

        $geoCity = $this->get('geo_city.identity')->getGeoCity();

        $q = $em->createNativeQuery("
            SELECT t.*
            FROM (
                SELECT
                    CASE WHEN gp.geo_city_id = :geo_city_id THEN true ELSE false END AS is_available,
                    gr.geo_point_id,
                    dgr.geo_point_id AS destination_geo_point_id,
                    CASE WHEN grrc.destination_geo_room_id IS NOT NULL THEN
                        CASE WHEN dgp.geo_city_id = :geo_city_id THEN
                            CASE WHEN grrc.goods_release_id IS NOT NULL THEN 'movement' ELSE 'transit' END
                        ELSE
                            CASE WHEN grrc.goods_release_id IS NOT NULL THEN 'other-movement' ELSE 'other-transit' END
                        END
                    ELSE 'other-free' END AS transit_type,
                    NULL AS goods_pallet_id,
                    grrc.goods_release_id,
                    grrc.delta AS quantity
                FROM goods_reserve_register_current AS grrc
                LEFT OUTER JOIN geo_room AS gr ON gr.id = grrc.geo_room_id
                LEFT OUTER JOIN geo_point AS gp ON gp.id = gr.geo_point_id
                LEFT OUTER JOIN geo_room AS dgr ON dgr.id = grrc.destination_geo_room_id
                LEFT OUTER JOIN geo_point AS dgp ON dgp.id = dgr.geo_point_id
                WHERE grrc.base_product_id = :base_product_id AND grrc.goods_condition_code = 'free'::goods_condition_code
                    AND grrc.goods_pallet_id IS NULL

                UNION ALL

                SELECT
                    false AS is_available,
                    gr.geo_point_id,
                    o.geo_point_id AS destination_geo_point_id,
                    CASE WHEN gp.geo_city_id = :geo_city_id THEN 'pallet' ELSE 'other-pallet' END AS transit_type,
                    grrc.goods_pallet_id,
                    NULL AS goods_release_id,
                    grrc.delta AS quantity
                FROM goods_reserve_register_current AS grrc
                INNER JOIN geo_room AS gr ON gr.id = grrc.geo_room_id
                INNER JOIN order_item AS oi ON oi.id = grrc.order_item_id
                INNER JOIN order_doc AS o ON o.did = oi.order_did
                INNER JOIN geo_point AS gp ON gp.id = o.geo_point_id
                WHERE grrc.base_product_id = :base_product_id AND grrc.goods_condition_code = 'free'::goods_condition_code
                    AND grrc.goods_pallet_id IS NOT NULL
            ) AS t
            ORDER BY t.is_available DESC
        ", new DTORSM(DTO\FreeReserve::class));
        $q->setParameter('geo_city_id', $geoCity->getId());
        $q->setParameter('base_product_id', $baseProduct->getId());
        $reserves = $q->getResult('DTOHydrator');

        // Резервов нет
        if (empty($reserves)) {
            if ($baseProduct->getSupplierId() === null) {
                return new DTO\DeliveryDate(ProductAvailabilityCode::OUT_OF_STOCK);
            }

            if ($baseProduct->getSupplierAvailabilityCode() === ProductAvailabilityCode::ON_DEMAND) {
                return new DTO\DeliveryDate(ProductAvailabilityCode::AWAITING);
            }

            if ($baseProduct->getSupplierAvailabilityCode() === ProductAvailabilityCode::AVAILABLE) {
                $supplier = $em->getRepository(Supplier::class)->find($baseProduct->getSupplierId());
                if (!$supplier instanceof Supplier) {
                    throw new NotFoundHttpException(sprintf('Поставщик %d не найден', $baseProduct->getSupplierId()));
                }

                $delivery = new DTO\DeliveryDate(ProductAvailabilityCode::ON_DEMAND);
                $delivery->date = $supplier->getOrderDeliveryDate();

                return $delivery;
            }

            return new DTO\DeliveryDate(ProductAvailabilityCode::OUT_OF_STOCK);
        }

        // В наличии на этой точке
        $available = array_filter($reserves, function($reserve) { return $reserve->isAvailable; });
        if (!empty($available)) {
            $geoPointIds = array_map(function($reserve) { return $reserve->geoPointId; }, $available);
            $q = $em->createNativeQuery("
                SELECT
                    gp.id,
                    gp.code,
                    gp.name,
                    0 AS quantity
                FROM geo_point AS gp
                INNER JOIN representative AS r ON r.geo_point_id = gp.id
                WHERE gp.id IN (:geo_point_ids)
                ORDER BY r.is_central DESC
            ", new DTORSM(DTO\GeoPoint::class, DTORSM::ARRAY_ASSOC));
            $q->setParameter('geo_point_ids', $geoPointIds);
            $geoPoints = $q->getResult('DTOHydrator');
            foreach ($available as $reserve) {
                $geoPoints[$reserve->geoPointId]->quantity = $reserve->quantity;
            }

            $delivery = new DTO\DeliveryDate(ProductAvailabilityCode::AVAILABLE);
            $delivery->geoPoints = array_values($geoPoints);

            return $delivery;
        }

        $delivery = new DTO\DeliveryDate(ProductAvailabilityCode::ON_DEMAND);

        // В пути на эту точку с другой точки
        $movement = array_filter($reserves, function($reserve) { return $reserve->transitType == 'movement'; });
        if (!empty($movement)) {
            foreach ($movement as $reserve) {
                $delivery->setDate($this->getDateByRoute($reserve->geoPointId, $reserve->destinationGeoPointId));
            }
        }
        // В пути на эту точку от поставщика
        $transit = array_filter($reserves, function($reserve) { return $reserve->transitType == 'transit'; });
        if (!empty($transit)) {
            foreach ($transit as $reserve) {
                $delivery->setDate($this->getDateByRoute($reserve->geoPointId, $reserve->destinationGeoPointId));
            }
        }
        // В палете на другой точке для этой точки
        $pallet = array_filter($reserves, function($reserve) { return $reserve->transitType == 'pallet'; });
        if (!empty($pallet)) {
            foreach ($pallet as $reserve) {
                $delivery->setDate($this->getDateByRoute($reserve->geoPointId, $reserve->destinationGeoPointId));
            }
        }
        if ($delivery->date !== null) {
            return $delivery;
        }

        // Свободные остатки на других точках
        $free = array_filter($reserves, function($reserve) { return $reserve->transitType == 'other-free'; });
        if (!empty($free)) {
            foreach ($free as $reserve) {
                $delivery->setDate($this->getDateByRoute($reserve->geoPointId, $reserve->destinationGeoPointId));
            }
        }
        if ($delivery->date !== null) {
            return $delivery;
        }

        // Центральная точка города
        $q = $em->createNativeQuery("
            SELECT
                gp.id,
                gp.code,
                gp.name,
                0 AS quantity
            FROM geo_point AS gp
            INNER JOIN representative AS r ON r.geo_point_id = gp.id
            WHERE gp.geo_city_id = :geo_city_id AND r.is_central = true
        ", new DTORSM(DTO\GeoPoint::class, DTORSM::OBJECT_SINGLE));
        $q->setParameter('geo_city_id', $geoCity->getId());
        $currentGeoPoint = $q->getResult('DTOHydrator');
        if (!$currentGeoPointId instanceof DTO\GeoPoint) {
            throw new NotFoundHttpException(sprintf('В %s не найдена центральная точка', $geoCity->getName()));
        }

        // В пути на другую точку с другой точки
        $movement = array_filter($reserves, function($reserve) { return $reserve->transitType == 'other-movement'; });
        if (!empty($movement)) {
            foreach ($movement as $reserve) {
                $date = $this->getDateByRoute($reserve->geoPointId, $reserve->destinationGeoPointId);
                $delivery->setDate($this->getDateByRoute($reserve->destinationGeoPointId, $currentGeoPointId));
            }
        }
        $transit = array_filter($reserves, function($reserve) { return $reserve->transitType == 'other-transit'; });
        if (!empty($transit)) {
            foreach ($transit as $reserve) {
                $date = $this->getDateByRoute($reserve->geoPointId, $reserve->destinationGeoPointId);
                $delivery->setDate($this->getDateByRoute($reserve->destinationGeoPointId, $currentGeoPointId));
            }
        }
        $pallet = array_filter($reserves, function($reserve) { return $reserve->transitType == 'other-pallet'; });
        if (!empty($pallet)) {
            foreach ($pallet as $reserve) {
                $date = $this->getDateByRoute($reserve->geoPointId, $reserve->destinationGeoPointId);
                $delivery->setDate($this->getDateByRoute($reserve->destinationGeoPointId, $currentGeoPointId));
            }
        }

        return $delivery;
    }

    protected function getDateByRoute(int $startingPointId, int $arrivalPointId): \DateTime
    {
        $routes = $this->getRoutes();
        if (isset($routes[$startingPointId][$arrivalPointId])) {
            return $this->getCron($routes[$startingPointId][$arrivalPointId]])->getNextRunDate();
        }

        $date = new \DateTime(); // now

        $routes = $this->getAllRoutes();
        $queue = array_keys($routes[$fromPointId = $startingPointId]);
        $visited = [];

        while (length($queue)) {
            $nextPointId = array_shift($queue);

            if (!isset($visited[$nextPointId])) {
                $date = $this->getCron($routes[$fromPointId][$nextPointId])->getNextRunDate($date);

                if ($nextPointId === $arrivalPointId) {
                    return $date;
                }

                if (isset($routes[$nextPointId])) {
                    $queue = array_merge($queue, array_keys($routes[$nextPointId]));
                }

                $visited[] = $fromPointId = $nextPointId;
            }
        }

        throw new \RuntimeException(sprintf('Маршрута из точки %d в точку %d не существует', $startingPointId, $arrivalPointId));
    }

    protected function getCron(string $expression): CronExpression
    {
        if (null === $this->cron) {
            $this->cron = CronExpression::factory('* * * * *');
        }

        return $this->cron->setExpression($expression);
    }

    protected function getAllRoutes(): array
    {
        if (null === $this->routes) {
            $q = $this->getDoctrine()->getManager()->createNativeQuery("
                SELECT
                    starting_point_id,
                    arrival_point_id,
                    schedule
                FROM geo_point_route
            ", new DTORSM(DTO\GeoPointRoute::class));
            $routes = $q->getResult('DTOHydrator');

            foreach ($routes as $route) {
                $this->routes[$route->startingPointId][$route->arrivalPointId] = $route->schedule;
            }
        }

        return $this->routes;
    }
}
