<?php

namespace OrgBundle\Components\Salary;

use Doctrine\ORM\Query\ResultSetMapping;
use OrgBundle\Entity\ActivityIndex;

class OverstockedGoods extends Base\AbstractComponent
{
    protected $with;

    protected function init()
    {
        parent::init();

        $this->with = /** @lang PostgreSQL */
            'WITH goods_log AS (
                SELECT
                  grrc.base_product_id,
                  grrc.supply_item_id,
                  grrc.geo_room_id,
                  grrc.delta AS delta_last,
                  grr.register_operation_type_code AS operation_type,
                  grr.registered_at,
                  SUM(grr.delta)
                    OVER (
                      PARTITION BY
                        grrc.base_product_id,
                        grrc.supply_item_id,
                        grrc.geo_room_id
                      ORDER BY grr.registered_at
                    )
                    AS delta_log
                FROM goods_reserve_register_current AS grrc
                  INNER JOIN goods_reserve_register AS grr
                    ON grrc.base_product_id = grr.base_product_id AND grrc.geo_room_id = grr.geo_room_id
                WHERE grrc.goods_condition_code = \'free\' AND grrc.delta > 0
              ),
            goods_stat AS (
                SELECT
                  gl.base_product_id,
                  gl.supply_item_id,
                  gl.geo_room_id,
                  MIN(gl.delta_last) AS quantity,
                  MAX(gl.registered_at)
                    FILTER (WHERE gl.delta_log <= 0)                      AS last_zero,
                  MAX(gl.registered_at)
                    FILTER (WHERE gl.operation_type = \'order_receipt\')    AS last_sell,
                  MIN(gl.registered_at)
                    FILTER (WHERE gl.operation_type = \'goods_acceptance\') AS first_supply,
                  array_agg(gl.registered_at ORDER BY gl.registered_at DESC)
                    FILTER (WHERE gl.operation_type = \'goods_acceptance\') AS supply_all
                FROM goods_log AS gl
                GROUP BY base_product_id, supply_item_id, geo_room_id
              ),
            goods_over AS (
                SELECT
                  gs.base_product_id,
                  gs.supply_item_id,
                  gs.geo_room_id,
                  gs.quantity,
            --       gs.last_zero,
            --       gs.last_sell,
            --       gs.first_supply,
            --       gs.supply_all,
                  CASE
                    WHEN gs.last_sell IS NULL
                      THEN gs.first_supply
                    WHEN gs.last_zero IS NULL OR gs.last_sell > gs.last_zero
                      THEN gs.last_sell
                    WHEN gs.supply_all[5] IS NOT NULL AND gs.supply_all[5] > gs.last_zero
                      THEN gs.supply_all[5]
                    WHEN gs.supply_all[4] IS NOT NULL AND gs.supply_all[4] > gs.last_zero
                      THEN gs.supply_all[4]
                    WHEN gs.supply_all[3] IS NOT NULL AND gs.supply_all[3] > gs.last_zero
                      THEN gs.supply_all[3]
                    WHEN gs.supply_all[2] IS NOT NULL AND gs.supply_all[2] > gs.last_zero
                      THEN gs.supply_all[2]
                    ELSE gs.supply_all[1]
                  END AS registered_at
                FROM goods_stat AS gs
              )';

        $this->from['go'] = 'goods_over AS go';
    }

    /**
     * @inheritDoc
     */
    protected function constructIndexQuery(ActivityIndex $activityIndex)
    {
        switch ($activityIndex->getCode()) {
            case 'quantity':
                $this->select[] = 'SUM(go.quantity) AS ' . $activityIndex->getCode();

                break;
            case 'supply':
            default:
                $this->select[] = 'SUM(si.purchase_price * go.quantity) AS ' . $activityIndex->getCode();

                $this->from['si']  = 'INNER JOIN supply_item AS si ON go.supply_item_id = si.id';
                break;
        }
    }

    /**
     * @inheritDoc
     */
    protected function constructDateQuery($since, $till)
    {
        $this->clause[] = ':since <= :till';
        $this->params['since'] = $since;
        $this->params['till'] = $till;
    }

    /**
     * @inheritDoc
     */
    protected function constructPointQuery($pointId)
    {
        $this->from['gr'] = 'INNER JOIN geo_room AS gr ON go.geo_room_id = gr.id';
        $this->from['rr'] = 'INNER JOIN representative AS rr ON gr.geo_point_id = rr.geo_point_id';

        $this->clause[] = 'rr.org_department_id IN (:geoPointId)';
        $this->params['geoPointId'] = $pointId;
    }

    /**
     * @inheritDoc
     */
    protected function constructCityQuery($cityId)
    {
        $this->from['gr'] = 'INNER JOIN geo_room AS gr ON go.geo_room_id = gr.id';
        $this->from['gp'] = 'INNER JOIN geo_point AS gp ON gr.geo_point_id = gp.id';

        $this->clause[] = 'gp.geo_city_id IN (:cityId)';
        $this->params['cityId'] = $cityId;
    }

    /**
     * @inheritDoc
     */
    protected function constructAreaQuery($areaId)
    {
        $this->from['gr']  = 'INNER JOIN geo_room AS gr ON go.geo_room_id = gr.id';
        $this->from['rr']  = 'INNER JOIN representative AS rr ON gr.geo_point_id = rr.geo_point_id';
        $this->from['dtd'] = 'INNER JOIN org_department_to_department AS dtd ON rr.org_department_id = dtd.org_department_id
                AND dtd.active_since <= :till AND (dtd.active_till IS NULL OR dtd.active_till >= :since)';
        $this->from['dp']  = 'INNER JOIN org_department_path AS dp ON rr.org_department_id = dp.org_department_id';

        $this->clause[] = 'dp.pid IN (:areaId)';
        $this->params['areaId'] = $areaId;
    }

    /**
     * @inheritDoc
     */
    protected function constructCategoryQuery($categoryId)
    {
        $this->from['bp'] = 'INNER JOIN base_product AS bp ON go.base_product_id = bp.id';
        $this->from['cp'] = 'INNER JOIN category_path AS cp ON bp.category_id = cp.id';

        $this->clause[] = 'cp.pid IN (:categoryId)';
        $this->params['categoryId'] = $categoryId;
    }

    /**
     * @inheritDoc
     */
    protected function constructIntervalQuery($interval)
    {
        $this->clause[] = "go.registered_at + INTERVAL '" . intval($interval) . " month' >= :since";
    }

    /**
     * @inheritDoc
     */
    protected function executeQuery()
    {
        if ($this->select && $this->from) {
            $query = $this->em->createNativeQuery(
                    ($this->with ? $this->with : '') . '
                    SELECT ' . implode(', ', $this->select) . '
                    FROM ' . implode(' ', $this->from) .
                    ($this->clause ? ' WHERE (' . implode(') AND (', $this->clause) . ')' : '') .
                    ($this->group ? ' GROUP BY ' . implode(', ', $this->group) : '') .
                    ($this->order ? ' ORDER BY ' . implode(', ', $this->order) : '')
                , new ResultSetMapping());

            if ($this->params) {
                $query->setParameters($this->params);
            }

            return $query->getResult('ListHydrator');
        }
        return null;
    }
}

/*
Запрос для проверки:

WITH goods_log AS (
    SELECT
      grrc.base_product_id,
      grrc.supply_item_id,
      grrc.geo_room_id,
      grrc.delta AS delta_last,
      grr.register_operation_type_code AS operation_type,
      grr.registered_at,
      SUM(grr.delta)
        OVER (
          PARTITION BY
            grrc.base_product_id,
            grrc.supply_item_id,
            grrc.geo_room_id
          ORDER BY grr.registered_at
        )
        AS delta_log
    FROM goods_reserve_register_current AS grrc
      INNER JOIN goods_reserve_register AS grr
        ON grrc.base_product_id = grr.base_product_id
          AND grrc.geo_room_id = grr.geo_room_id
          AND grrc.supply_item_id = grr.supply_item_id
    WHERE grrc.goods_condition_code = 'free' AND grrc.delta > 0
  ),
goods_stat AS (
    SELECT
      gl.base_product_id,
      gl.supply_item_id,
      gl.geo_room_id,
      MIN(gl.delta_last) AS quantity,
      MAX(gl.registered_at)
        FILTER (WHERE gl.delta_log <= 0)                      AS last_zero,
      MAX(gl.registered_at)
        FILTER (WHERE gl.operation_type = 'order_receipt')    AS last_sell,
      MIN(gl.registered_at)
        FILTER (WHERE gl.operation_type = 'goods_acceptance') AS first_supply,
      array_agg(gl.registered_at ORDER BY gl.registered_at DESC)
        FILTER (WHERE gl.operation_type = 'goods_acceptance') AS supply_all
    FROM goods_log AS gl
    GROUP BY base_product_id, supply_item_id, geo_room_id
  ),
goods_over AS (
    SELECT
      gs.base_product_id,
      gs.supply_item_id,
      gs.geo_room_id,
      gs.quantity,
--       gs.last_zero,
--       gs.last_sell,
--       gs.first_supply,
--       gs.supply_all,
      CASE
        WHEN gs.last_sell IS NULL
          THEN gs.first_supply
        WHEN gs.last_zero IS NULL OR gs.last_sell > gs.last_zero
          THEN gs.last_sell
        WHEN gs.supply_all[5] IS NOT NULL AND gs.supply_all[5] > gs.last_zero
          THEN gs.supply_all[5]
        WHEN gs.supply_all[4] IS NOT NULL AND gs.supply_all[4] > gs.last_zero
          THEN gs.supply_all[4]
        WHEN gs.supply_all[3] IS NOT NULL AND gs.supply_all[3] > gs.last_zero
          THEN gs.supply_all[3]
        WHEN gs.supply_all[2] IS NOT NULL AND gs.supply_all[2] > gs.last_zero
          THEN gs.supply_all[2]
        ELSE gs.supply_all[1]
      END AS registered_at
    FROM goods_stat AS gs
  )
SELECT
--   cp.pid AS kval,
  dp.pid AS kval,
--   rr.org_department_id AS kval,
  SUM(go.quantity) AS quantity,
  SUM(si.purchase_price * go.quantity) AS supply
FROM goods_over AS go
  INNER JOIN supply_item AS si ON go.supply_item_id = si.id

--   Категория
--   INNER JOIN base_product AS bp ON go.base_product_id = bp.id
--   INNER JOIN category_path AS cp ON bp.category_id = cp.id

--   Гео точка
  INNER JOIN geo_room AS gr ON go.geo_room_id = gr.id
--   Город
--   INNER JOIN geo_point AS gp ON gr.geo_point_id = gp.id
--   Гео зона
  INNER JOIN representative AS rr ON gr.geo_point_id = rr.geo_point_id
  INNER JOIN org_department_to_department AS dtd ON rr.org_department_id = dtd.org_department_id
    AND dtd.active_since <= '2018-07-01' AND (dtd.active_till IS NULL OR dtd.active_till >= '2018-06-01')
  INNER JOIN org_department_path AS dp ON rr.org_department_id = dp.org_department_id

WHERE
  go.registered_at >= CURRENT_TIMESTAMP - INTERVAL '12 month'

GROUP BY kval
ORDER BY quantity DESC;
 */