<?php

namespace AppBundle\Bus\Product\Query;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use AppBundle\Doctrine\ORM\Query\DTORSM;
use AppBundle\Enum\GoodsConditionCode;
use AppBundle\Enum\ProductAvailabilityCode;
use Cron\CronExpression;
use Doctrine\ORM\Query\ResultSetMapping;

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
                bp.supplier_availability_code AS supplier_availability,
                s.order_delivery_date AS supplier_delivery_date,
                (
                    SELECT r.geo_point_id
                    FROM representative AS r
                    INNER JOIN geo_point AS gp ON gp.id = r.geo_point_id
                    WHERE gp.geo_city_id = :geo_city_id AND r.is_active = TRUE AND r.is_central = TRUE
                ) as geo_point_id
            FROM product AS p
            INNER JOIN base_product AS bp ON bp.canonical_id = p.base_product_id
            LEFT OUTER JOIN supplier AS s ON s.id = bp.supplier_id
            WHERE bp.id = :base_product_id AND p.geo_city_id IN (0, :geo_city_id)
            ORDER BY p.geo_city_id DESC
            LIMIT 1
        ', new DTORSM(DTO\Product::class, DTORSM::OBJECT_SINGLE));
        $q->setParameter('base_product_id', $query->baseProductId);
        $q->setParameter('geo_city_id', $geoCity->getId());
        $product = $q->getResult('DTOHydrator');
        $product->geoPointId = $product->geoPointId ?? $this->getParameter('default.point.id');

        if (!$product instanceof DTO\Product) {
            throw new NotFoundHttpException(sprintf('Товар с кодом %d не найден', $query->baseProductId));
        }
        if (!in_array($product->availability, [ProductAvailabilityCode::ON_DEMAND, ProductAvailabilityCode::IN_TRANSIT])) {
            throw new BadRequestHttpException(sprintf('Товар %d находится либо в наличии, либо отсутствует', $query->baseProductId));
        }

        $q = $em->createNativeQuery("
            SELECT
                gr.geo_point_id,
                COALESCE(gpal.geo_point_id, dgr.geo_point_id) AS destination_geo_point_id,
                CASE WHEN gpal.geo_point_id IS NOT NULL THEN 'pallet'
                WHEN grrc.destination_geo_room_id IS NOT NULL THEN
                    CASE WHEN dgp.geo_city_id = :geo_city_id
                    THEN 'transit'
                    ELSE 'other-transit' END
                ELSE 'other-free' END AS transit_type,
                COALESCE(to_char(gad.arriving_time, 'YYYY-MM-DD')::date, sd.arriving_date) AS arriving_date
            FROM goods_reserve_register_current AS grrc
            LEFT OUTER JOIN geo_room AS gr ON gr.id = grrc.geo_room_id
            LEFT OUTER JOIN geo_point AS gp ON gp.id = gr.geo_point_id
            LEFT OUTER JOIN geo_room AS dgr ON dgr.id = grrc.destination_geo_room_id
            LEFT OUTER JOIN geo_point AS dgp ON dgp.id = dgr.geo_point_id
            LEFT OUTER JOIN order_item AS oi ON oi.id = grrc.order_item_id
            LEFT OUTER JOIN order_doc AS od ON od.did = oi.order_did
            LEFT OUTER JOIN supply_item AS si ON si.id = grrc.supply_item_id
            LEFT OUTER JOIN supply_doc AS sd ON sd.did = si.parent_did AND grrc.goods_release_id IS NULL AND grrc.geo_room_id IS NULL
            LEFT OUTER JOIN goods_release_doc AS grd ON grd.did = grrc.goods_release_id
            LEFT OUTER JOIN goods_acceptance_doc AS gad ON grd.did = gad.parent_doc_did
            LEFT OUTER JOIN geo_room AS sgr ON sgr.id = sd.destination_room_id
            LEFT OUTER JOIN goods_pallet AS gpal ON gpal.id = grrc.goods_pallet_id
            WHERE grrc.base_product_id = :base_product_id AND grrc.goods_condition_code = 'free'::goods_condition_code
        ", new DTORSM(DTO\FreeReserve::class));
        $q->setParameter('base_product_id', $query->baseProductId);
        $q->setParameter('geo_city_id', $geoCity);
        $q->setParameter('goods_condition_code_FREE', GoodsConditionCode::FREE);

        $reserves = $q->getResult('DTOHydrator');

        $delivery = new DTO\DeliveryDate();

        $defaultGeoPointId = $this->getParameter('default.point.id');

        if (!empty($reserves)) {
            // В пути на эту точку
            $transitToDestination = array_filter($reserves, function ($reserve) {
                return 'transit' == $reserve->transitType;
            });

            if (!empty($transitToDestination)) {
                foreach ($transitToDestination as $reserve) {
                    $date = $reserve->arrivingDate;

                    if (empty($delivery->date) || $delivery->date > $date) {
                        $delivery->setDate($date);
                    }
                }

                if (null !== $delivery->date) {
                    return $delivery;
                }
            }
        }

        // Резервов нет, но возможно есть у поставщика
        if (ProductAvailabilityCode::AVAILABLE == $product->supplierAvailability) {
            if ($product->geoPointId == $defaultGeoPointId) {
                $delivery->setDate($product->supplierDeliveryDate);
            } else {
                $delivery->setDate($this->getDateByRoute($defaultGeoPointId, $product->geoPointId, $product->supplierDeliveryDate));
            }

            return $delivery;
        }

        if (!empty($reserves)) {
            // Резервы на других точках
            $atAnotherPoint = array_filter($reserves, function ($reserve) { return 'other-free' == $reserve->transitType; });

            if (!empty($atAnotherPoint)) {
                foreach ($atAnotherPoint as $reserve) {
                    $date = $this->getDateByRoute($reserve->geoPointId, $product->geoPointId, new \DateTime());

                    if (empty($delivery->date) || $delivery->date > $date) {
                        $delivery->setDate($date);
                    }
                }

                if (null !== $delivery->date) {
                    return $delivery;
                }
            }

            // В пути на другую точку
            $transitToAnotherPoint = array_filter($reserves, function ($reserve) { return 'other-transitt' == $reserve->transitType; });

            if (!empty($transitToAnotherPoint)) {
                foreach ($transitToAnotherPoint as $reserve) {
                    $date = $this->getDateByRoute($reserve->destinationGeoPointId, $product->geoPointId, $reserve->arrivingDate);

                    if (empty($delivery->date) || $delivery->date > $date) {
                        $delivery->setDate($date);
                    }
                }

                if (null !== $delivery->date) {
                    return $delivery;
                }
            }

            // В паллете на точке
            $pallet = array_filter($reserves, function ($reserve) { return 'pallet' == $reserve->transitType; });
            if (!empty($pallet)) {
                foreach ($pallet as $reserve) {
                    $date = $this->getDateByRoute($reserve->geoPointId, $reserve->destinationGeoPointId, new \DateTime());
                    $date = $this->getDateByRoute($reserve->destinationGeoPointId, $product->geoPointId, $date);

                    if (empty($delivery->date) || $delivery->date > $date) {
                        $delivery->setDate($date);
                    }
                }
            }

            return $delivery;
        }
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
            $route = $routes[$startingGeoPointId][$arrivalGeoPointId];
            $nth = !empty($route['next_week_condition']) && $this->getCron($route['next_week_condition'])->isDue() ? 1 : 0;

            return $this->getCron($route['schedule'])->getNextRunDate($date, $nth);
        }

        $queue = array_keys($routes[$fromGeoPointId = $startingGeoPointId]);
        $visited = [$fromGeoPointId => $fromGeoPointId];

        while (count($queue)) {
            $nextGeoPointId = array_shift($queue);

            if (!isset($visited[$nextGeoPointId])) {
                if (isset($routes[$fromGeoPointId][$nextGeoPointId])) {
                    $route = $routes[$fromGeoPointId][$nextGeoPointId];
                    $nth = !empty($route['next_week_condition']) && $this->getCron($route['next_week_condition'])->isDue() ? 1 : 0;
                    $date = $this->getCron($route['schedule'])->getNextRunDate($date, $nth);

                    if ($nextGeoPointId === $arrivalGeoPointId) {
                        return $date;
                    }

                    if (isset($routes[$nextGeoPointId])) {
                        //@TODO: костыль, скорее всего работает только если точка находится на втором прыжке маршрута
                        foreach ($queue as $q) {
                            if ($q === $arrivalGeoPointId && isset($routes[$fromGeoPointId][$q])) {
                                $route = $routes[$fromGeoPointId][$q];
                                $nth = !empty($route['next_week_condition']) && $this->getCron($route['next_week_condition'])->isDue() ? 1 : 0;

                                return $this->getCron($route['schedule'])->getNextRunDate($date);
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
                    schedule,
                    next_week_condition
                FROM geo_point_route
            ', new ResultSetMapping());
            $rows = $q->getResult('ListAssocHydrator');

            $this->routes = [];
            foreach ($rows as $row) {
                $this->routes[$row['starting_point_id']][$row['arrival_point_id']] = [
                    'schedule' => $row['schedule'],
                    'next_week_condition' => $row['next_week_condition'],
                ];
            }
        }

        return $this->routes;
    }
}
