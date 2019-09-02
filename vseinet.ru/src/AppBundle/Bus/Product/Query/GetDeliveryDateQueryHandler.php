<?php

namespace AppBundle\Bus\Product\Query;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use AppBundle\Doctrine\ORM\Query\DTORSM;
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
        $geoCity = $this->get('geo_city.identity')->getGeoCity();

        $q = $em->createNativeQuery('
            SELECT
                p.base_product_id,
                p.product_availability_code AS availability,
                bp.supplier_id,
                bp.supplier_availability_code AS supplier_availability
            FROM product AS p
            INNER JOIN base_product AS bp ON bp.canonical_id = p.base_product_id
            WHERE bp.id = :base_product_id AND p.geo_city_id IN (0, :geo_city_id)
            ORDER BY p.geo_city_id DESC
            LIMIT 1
        ', new DTORSM(DTO\Product::class, DTORSM::OBJECT_SINGLE));
        $q->setParameter('base_product_id', $query->baseProductId);
        $q->setParameter('geo_city_id', $geoCity->getId());
        $product = $q->getResult('DTOHydrator');
        if (!$product instanceof DTO\Product) {
            throw new NotFoundHttpException(sprintf('Товар с кодом %d не найден', $query->baseProductId));
        }
        if (!in_array($product->availability, [ProductAvailabilityCode::ON_DEMAND, ProductAvailabilityCode::IN_TRANSIT])) {
            throw new BadRequestHttpException(sprintf('Товар %d находится либо в наличии, либо отсутствует', $query->baseProductId));
        }

        $q = $em->createNativeQuery("
            SELECT
                gr.geo_point_id,
                dgr.geo_point_id AS destination_geo_point_id,
                CASE WHEN grrc.destination_geo_room_id IS NOT NULL THEN
                    CASE WHEN od.geo_city_id = :geo_city_id OR (dgp.geo_city_id = :geo_city_id AND grrc.order_item_id IS NULL) THEN
                        CASE WHEN grrc.goods_release_id IS NOT NULL THEN 'movement' ELSE 'transit' END
                    ELSE
                        CASE WHEN grrc.goods_release_id IS NOT NULL THEN 'other-movement' ELSE 'other-transit' END
                    END
                ELSE 'other-free' END AS transit_type,
                grrc.delta AS quantity,
                sd.arriving_date,
                agr.geo_point_id AS arriving_geo_point_id
            FROM goods_reserve_register_current AS grrc
            LEFT OUTER JOIN geo_room AS gr ON gr.id = grrc.geo_room_id
            LEFT OUTER JOIN geo_point AS gp ON gp.id = gr.geo_point_id
            LEFT OUTER JOIN geo_room AS dgr ON dgr.id = grrc.destination_geo_room_id
            LEFT OUTER JOIN geo_point AS dgp ON dgp.id = dgr.geo_point_id
            LEFT OUTER JOIN order_item AS oi ON oi.id = grrc.order_item_id
            LEFT OUTER JOIN order_doc AS od ON od.did = oi.order_did
            LEFT OUTER JOIN supply_item AS si ON si.id = grrc.supply_item_id
            LEFT OUTER JOIN supply_doc AS sd ON sd.did = si.parent_did
            LEFT OUTER JOIN geo_room AS agr ON agr.id = sd.destination_room_id
            WHERE grrc.base_product_id = :base_product_id AND grrc.goods_condition_code = 'free'::goods_condition_code
                AND grrc.goods_pallet_id IS NULL

            UNION ALL

            SELECT
                gr.geo_point_id,
                o.geo_point_id AS destination_geo_point_id,
                CASE WHEN gp.geo_city_id = :geo_city_id THEN 'pallet' ELSE 'other-pallet' END AS transit_type,
                grrc.delta AS quantity,
                NULL AS arriving_date,
                NULL AS arriving_geo_point_id
            FROM goods_reserve_register_current AS grrc
            INNER JOIN geo_room AS gr ON gr.id = grrc.geo_room_id
            INNER JOIN order_item AS oi ON oi.id = grrc.order_item_id
            INNER JOIN order_doc AS o ON o.did = oi.order_did
            INNER JOIN geo_point AS gp ON gp.id = o.geo_point_id
            WHERE grrc.base_product_id = :base_product_id AND grrc.goods_condition_code = 'free'::goods_condition_code
                AND grrc.goods_pallet_id IS NOT NULL
        ", new DTORSM(DTO\FreeReserve::class));
        $q->setParameter('geo_city_id', $geoCity->getId());
        $q->setParameter('base_product_id', $product->baseProductId);
        $reserves = $q->getResult('DTOHydrator');

        // Резервов нет, но товар на заказ, значить есть у поставщика
        if (empty($reserves)) {
            if (ProductAvailabilityCode::AVAILABLE !== $product->supplierAvailability) {
                throw new BadRequestHttpException(sprintf('Товар %d отсутсвует у постащика', $product->baseProductId));
            }

            $supplier = $em->getRepository(Supplier::class)->find($product->supplierId);
            if (!$supplier instanceof Supplier) {
                throw new NotFoundHttpException(sprintf('Поставщик %d не найден', $product->supplierId));
            }

            return new DTO\DeliveryDate($supplier->getOrderDeliveryDate());
        }

        // выбираем лучшую дату
        $delivery = new DTO\DeliveryDate();

        // В пути на эту точку с другой точки
        $movement = array_filter($reserves, function ($reserve) { return 'movement' == $reserve->transitType; });
        if (!empty($movement)) {
            foreach ($movement as $reserve) {
                $delivery->setDate($this->getDateByRoute($reserve->geoPointId, $reserve->destinationGeoPointId));
            }
        }
        // В пути на эту точку от поставщика
        $transit = array_filter($reserves, function ($reserve) { return 'transit' == $reserve->transitType; });
        if (!empty($transit)) {
            foreach ($transit as $reserve) {
                if ($reserve->arrivingGeoPointId == $reserve->destinationGeoPointId) {
                    $date = $reserve->arrivingDate;
                } else {
                    $date = $this->getDateByRoute($reserve->arrivingGeoPointId, $reserve->destinationGeoPointId, $reserve->arrivingDate);
                }
                $delivery->setDate($date);
            }
        }
        // В палете на другой точке для этой точки
        $pallet = array_filter($reserves, function ($reserve) { return 'pallet' == $reserve->transitType; });
        if (!empty($pallet)) {
            foreach ($pallet as $reserve) {
                $delivery->setDate($this->getDateByRoute($reserve->geoPointId, $reserve->destinationGeoPointId));
            }
        }
        if (null !== $delivery->date) {
            return $delivery;
        }

        // Центральная точка города
        $q = $em->createNativeQuery('
            SELECT
                gp.id,
                gp.geo_city_id,
                gp.code,
                gp.name
            FROM geo_point AS gp
            INNER JOIN representative AS r ON r.geo_point_id = gp.id
            WHERE gp.geo_city_id = :geo_city_id AND r.is_central = true
        ', new DTORSM(DTO\GeoPoint::class, DTORSM::OBJECT_SINGLE));
        $q->setParameter('geo_city_id', $geoCity->getId());
        $currentGeoPoint = $q->getResult('DTOHydrator');
        if (!$currentGeoPoint instanceof DTO\GeoPoint) {
            throw new NotFoundHttpException(sprintf('В городе %s не найдена центральная точка', $geoCity->getName()));
        }

        // Свободные остатки на других точках
        $free = array_filter($reserves, function ($reserve) { return 'other-free' == $reserve->transitType; });
        if (!empty($free)) {
            foreach ($free as $reserve) {
                if (null !== $reserve->arrivingGeoPointId) {
                    $date = $this->getDateByRoute($reserve->geoPointId, $reserve->arrivingGeoPointId);
                    $middleGeoPointId = $reserve->arrivingGeoPointId;
                } else {
                    $date = new \DateTime();
                    $middleGeoPointId = $reserve->geoPointId;
                }
                $delivery->setDate($this->getDateByRoute($middleGeoPointId, $currentGeoPoint->id, $date));
            }
        }
        if (null !== $delivery->date) {
            return $delivery;
        }

        // В пути на другую точку с другой точки
        $movement = array_filter($reserves, function ($reserve) { return 'other-movement' == $reserve->transitType; });
        if (!empty($movement)) {
            foreach ($movement as $reserve) {
                $date = $this->getDateByRoute($reserve->geoPointId, $reserve->destinationGeoPointId);
                $delivery->setDate($this->getDateByRoute($reserve->destinationGeoPointId, $currentGeoPoint->id, $date));
            }
        }
        // В пути от поставщика на другую точку
        $transit = array_filter($reserves, function ($reserve) { return 'other-transit' == $reserve->transitType; });
        if (!empty($transit)) {
            foreach ($transit as $reserve) {
                if ($reserve->arringGeoPointId == $reserve->destinationGeoPointId) {
                    $date = $reserve->arrivingDate;
                } else {
                    $date = $this->getDateByRoute($reserve->arringGeoPointId, $reserve->destinationGeoPointId, $reserve->arrivingDate);
                }
                $delivery->setDate($this->getDateByRoute($reserve->destinationGeoPointId, $currentGeoPoint->id, $date));
            }
        }
        // В палете на другую точку
        $pallet = array_filter($reserves, function ($reserve) { return 'other-pallet' == $reserve->transitType; });
        if (!empty($pallet)) {
            foreach ($pallet as $reserve) {
                $date = $this->getDateByRoute($reserve->geoPointId, $reserve->destinationGeoPointId);
                $delivery->setDate($this->getDateByRoute($reserve->destinationGeoPointId, $currentGeoPoint->id, $date));
            }
        }

        return $delivery;
    }

    protected function getDateByRoute(?int $startingGeoPointId, ?int $arrivalGeoPointId, ?\DateTime $startingDate = null): ?\DateTime
    {
        $date = $startingDate ?? new \DateTime();

        if ($startingGeoPointId === $arrivalGeoPointId) {
            return $date;
        }

        $routes = $this->getAllRoutes();

        if (!isset($routes[$startingGeoPointId])) {
            return null;
        }

        if (isset($routes[$startingGeoPointId][$arrivalGeoPointId])) {
            return $this->getCron($routes[$startingGeoPointId][$arrivalGeoPointId])->getNextRunDate($date);
        }

        $queue = array_keys($routes[$fromGeoPointId = $startingGeoPointId]);
        $visited = [$fromGeoPointId => $fromGeoPointId];

        while (count($queue)) {
            $nextGeoPointId = array_shift($queue);

            if (!isset($visited[$nextGeoPointId])) {
                if (isset($routes[$fromGeoPointId][$nextGeoPointId])) {
                    $date = $this->getCron($routes[$fromGeoPointId][$nextGeoPointId])->getNextRunDate($date);

                    if ($nextGeoPointId === $arrivalGeoPointId) {
                        return $date;
                    }

                    if (isset($routes[$nextGeoPointId])) {
                        //@TODO: костыль, скорее всего работает только если точка находится на втором прыжке маршрута
                        foreach ($queue as $q) {
                            if ($q === $arrivalGeoPointId && isset($routes[$fromGeoPointId][$q])) {
                                return $this->getCron($routes[$fromGeoPointId][$q])->getNextRunDate($date);
                            }
                        }

                        $queue = array_merge($queue, array_keys($routes[$nextGeoPointId]));
                    }
                    $fromGeoPointId = $nextGeoPointId;
                } else {
                    $fromGeoPointId = $startingGeoPointId;
                }

                $visited[$nextGeoPointId] = $nextGeoPointId;
            }
        }

        return null;
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
            $q = $this->getDoctrine()->getManager()->createNativeQuery('
                SELECT
                    starting_point_id,
                    arrival_point_id,
                    schedule
                FROM geo_point_route
            ', new DTORSM(DTO\GeoPointRoute::class));
            $routes = $q->getResult('DTOHydrator');

            foreach ($routes as $route) {
                $this->routes[$route->startingPointId][$route->arrivalPointId] = $route->schedule;
            }
        }

        return $this->routes;
    }
}
