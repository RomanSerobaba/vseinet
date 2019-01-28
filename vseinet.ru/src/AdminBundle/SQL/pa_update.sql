CREATE OR REPLACE FUNCTION pa_update(update_geo_city_id int, update_id int8 DEFAULT NULL, update_all bool DEFAULT FALSE)
  RETURNS void AS $BODY$
DECLARE
  start_time timestamp = NOW();
  product_found record;
BEGIN
  IF update_all = TRUE THEN
    EXECUTE '
      CREATE OR REPLACE TEMP VIEW product_update_register
      AS SELECT * FROM aggregation.product_' || update_geo_city_id;
  ELSE
    CREATE TEMP TABLE product_update_register AS SELECT * FROM public.product WITH NO DATA;
    ALTER TABLE product_update_register ADD CONSTRAINT product_update_register_pkey PRIMARY KEY (id);
    CREATE INDEX product_update_register_base_product_id_idx ON product_update_register USING btree (base_product_id ASC NULLS LAST);

    IF update_id IS NULL THEN
      EXECUTE '
        INSERT INTO product_update_register
        SELECT p.*
        FROM aggregation.product_ ' || update_geo_city_id || ' AS p
        INNER JOIN aggregation.product_update_register_' || update_geo_city_id || ' AS pur ON pur.id = p.id
        WHERE pur.queued_at <= $1 AND pur.is_updated = FALSE
        GROUP BY p.id'
      USING start_time;

      SELECT id INTO product_found FROM product_update_register LIMIT 1;
      IF NOT found THEN
        RETURN;
      END IF;
    ELSE
      EXECUTE '
        INSERT INTO product_update_register
        SELECT p.*
        FROM aggregation.product_' || update_geo_city_id || ' AS p
        WHERE p.id = $1'
      USING update_id;
    END IF;
  END IF;

  DROP TABLE IF EXISTS product_tmp;
  CREATE TEMP TABLE product_tmp AS SELECT * FROM public.product WITH NO DATA;
  ALTER TABLE product_tmp ADD CONSTRAINT product_tmp_pkey PRIMARY KEY (id);
  CREATE INDEX product_tmp_base_product_id_idx ON product_tmp USING btree (base_product_id ASC NULLS LAST);

  -- товары у поставщика
  INSERT INTO product_tmp (
    id,
    base_product_id,
    product_availability_code,
    manual_price,
    ultimate_price,
    temporary_price
  )
  SELECT
    p.id,
    p.base_product_id,
    (CASE WHEN bp.supplier_availability_code = 'in_transit' THEN 'awaiting' ELSE 'on_demand' END)::product_availability_code,
    p.manual_price,
    p.ultimate_price,
    p.temporary_price
  FROM product_update_register AS p
  INNER JOIN public.base_product AS bp ON bp.id = p.base_product_id
  WHERE bp.supplier_availability_code IN ('available', 'in_transit', 'on_demand');

  -- внутреннее перемещение и транзит от поставщика
  WITH
    data AS (
      SELECT p.id
      FROM public.goods_reserve_register_current AS grrc
      INNER JOIN product_update_register AS p ON p.base_product_id = grrc.base_product_id
      WHERE grrc.goods_condition_code = 'free' AND grrc.destination_geo_room_id IS NOT NULL
      GROUP BY p.id
      HAVING SUM(grrc.delta) > 0
    ),
    updated AS (
      UPDATE product_tmp
      SET product_availability_code = 'in_transit'::product_availability_code
      FROM data
      WHERE product_tmp.id = data.id
      RETURNING data.id
    )
  INSERT INTO product_tmp (
    id,
    base_product_id,
    product_availability_code,
    manual_price,
    ultimate_price,
    temporary_price
  )
  SELECT
    p.id,
    p.base_product_id,
    'in_transit'::product_availability_code,
    p.manual_price,
    p.ultimate_price,
    p.temporary_price
  FROM data
  INNER JOIN product_update_register AS p ON p.id = data.id
  WHERE data.id NOT IN (SELECT id FROM updated);

  -- свободные остатки в других городах
  WITH
    data AS (
      SELECT p.id
      FROM public.goods_reserve_register_current AS grrc
      INNER JOIN product_update_register AS p ON p.base_product_id = grrc.base_product_id
      WHERE grrc.goods_condition_code = 'free' AND grrc.geo_room_id NOT IN (
          SELECT gr.id
          FROM public.geo_room AS gr
          INNER JOIN public.geo_point AS gp ON gp.id = gr.geo_point_id
          WHERE gp.geo_city_id = update_geo_city_id
        )
      GROUP BY p.id
      HAVING SUM(grrc.delta) > 0
    ),
    updated AS (
      UPDATE product_tmp
      SET product_availability_code = 'on_demand'::product_availability_code
      FROM data
      WHERE product_tmp.id = data.id
      RETURNING data.id
    )
  INSERT INTO product_tmp (
    id,
    base_product_id,
    product_availability_code,
    manual_price,
    ultimate_price,
    temporary_price
  )
  SELECT
    p.id,
    p.base_product_id,
    'in_transit'::product_availability_code,
    p.manual_price,
    p.ultimate_price,
    p.temporary_price
  FROM data
  INNER JOIN product_update_register AS p ON p.id = data.id
  WHERE data.id NOT IN (SELECT id FROM updated);

  -- свободные остатки
  WITH
    data AS (
      SELECT p.id
      FROM public.goods_reserve_register_current AS grrc
      INNER JOIN product_update_register AS p ON p.base_product_id = grrc.base_product_id
      WHERE grrc.goods_condition_code = 'free' AND grrc.geo_room_id IN (
          SELECT gr.id
          FROM public.geo_room AS gr
          INNER JOIN public.geo_point AS gp ON gp.id = gr.geo_point_id
          WHERE gp.geo_city_id = update_geo_city_id
        )
      GROUP BY p.id
      HAVING SUM(grrc.delta) > 0
    ),
    updated AS (
      UPDATE product_tmp
      SET product_availability_code = 'available'::product_availability_code
      FROM data
      WHERE product_tmp.id = data.id
      RETURNING data.id
    )
  INSERT INTO product_tmp (
    id,
    base_product_id,
    product_availability_code,
    manual_price,
    ultimate_price,
    temporary_price
  )
  SELECT
    p.id,
    p.base_product_id,
    'available'::product_availability_code,
    p.manual_price,
    p.ultimate_price,
    p.temporary_price
  FROM data
  INNER JOIN product_update_register AS p ON p.id = data.id
  WHERE data.id NOT IN (SELECT id FROM updated);

  -- расчет цен
  UPDATE product_tmp
  SET
    price = data.price,
    price_type = data.price_type::product_price_type,
    profit = data.price - data.purchase_price,
    competitor_price = data.competitor_price
  FROM (
    WITH
      category_delivery AS (
        SELECT
          p.id AS product_id,
          COALESCE(
            (SELECT
              c.delivery_tax
            FROM public.base_product AS bp
            INNER JOIN public.category_path AS cp ON cp.id = bp.category_id
            INNER JOIN public.category AS c ON c.id = cp.pid
            WHERE c.delivery_tax > 0 AND bp.id = p.base_product_id
            ORDER BY cp.plevel DESC
            LIMIT 1), 0
          ) AS delivery_tax
        FROM product_tmp AS p
      ),
      competitor_product AS (
        SELECT
          p.id AS product_id,
          MIN(p2c.competitor_price) AS competitor_price
        FROM product_tmp AS p
        INNER JOIN product_to_competitor AS p2c ON p2c.base_product_id = p.base_product_id AND p2c.geo_city_id = update_geo_city_id
        INNER JOIN competitor AS c ON c.id = p2c.competitor_id
        WHERE c.is_active = TRUE AND p2c.competitor_price > 0
          AND
          CASE
            WHEN c.channel IN ('site', 'retail')
            THEN p2c.price_time + INTERVAL '7 day' >= NOW()
            ELSE p2c.price_time + INTERVAL '30 day' >= NOW()
          END
        GROUP BY p.id
      ),
      standard_product AS (
        SELECT
          p.id AS product_id,
          ROUND(bp.supplier_price * (
            SELECT
              tm.margin_percent
            FROM
              trade_margin AS tm
            INNER JOIN category_path AS cp ON cp.pid = tm.category_id
            WHERE cp.id = bp.category_id AND bp.supplier_price BETWEEN tm.lower_limit AND tm.higher_limit
            ORDER BY cp.plevel DESC
            LIMIT 1
          ) / 100, -2) AS retail_price
        FROM product_tmp AS p
        INNER JOIN base_product AS bp ON bp.id = p.base_product_id
      )
    SELECT
      p.id,
      bp.supplier_price AS purchase_price,
      CASE
        WHEN p.temporary_price > 0 AND p.product_availability_code = 'available' THEN p.temporary_price
        WHEN p.ultimate_price > 0 AND p.product_availability_code = 'available' THEN p.ultimate_price
        WHEN p.manual_price > 0 THEN p.manual_price
        WHEN cp.competitor_price > 0 AND sp.retail_price + cd.delivery_tax > cp.competitor_price
          AND cp.competitor_price > bp.supplier_price THEN cp.competitor_price * 0.3 + 0.7 * sp.retail_price - cd.delivery_tax
        WHEN bp.price_retail_min > sp.retail_price THEN bp.price_retail_min
        ELSE sp.retail_price
      END AS price,
      CASE
        WHEN p.temporary_price > 0 AND p.product_availability_code = 'available' THEN 'temporary'
        WHEN p.ultimate_price > 0 AND p.product_availability_code = 'available' THEN 'ultimate'
        WHEN p.manual_price > 0 THEN 'manual'
        WHEN cp.competitor_price > 0 AND sp.retail_price + cd.delivery_tax > cp.competitor_price
          AND cp.competitor_price > bp.supplier_price THEN 'compared'
        WHEN bp.price_retail_min > sp.retail_price THEN 'recommended'
        ELSE 'standard'
      END AS price_type,
      cp.competitor_price
    FROM product_tmp AS p
    INNER JOIN base_product AS bp ON bp.id = p.base_product_id
    LEFT JOIN category_delivery AS cd ON cd.product_id = p.id
    LEFT JOIN competitor_product AS cp ON cp.product_id = p.id
    LEFT JOIN standard_product AS sp ON sp.product_id = p.id
  ) AS data
  WHERE product_tmp.id = data.id;

  IF update_id IS NOT NULL THEN
    EXECUTE '
      UPDATE aggregation.product_' || update_geo_city_id || ' AS p
      SET
        p.product_availability_code = product_tmp.product_availability_code,
        p.price = product_tmp.price,
        p.price_type = product_tmp.price_type,
        p.price_time = CASE WHEN p.price = product_tmp.price THEN p.price_time ELSE NOW() END,
        p.profit = product_tmp.profit,
        p.competitor_price = product_tmp.competitor_price
      FROM product_tmp
    ';
  ELSE
    EXECUTE '
      UPDATE aggregation.product_' || update_geo_city_id || '
      SET
        product_availability_code = data.product_availability_code,
        price = data.price,
        price_type = data.price_type,
        price_time = CASE WHEN aggregation.product_' || update_geo_city_id || '.price = data.price THEN price_time ELSE NOW() END,
        profit = data.profit
        competitor_price = data.competitor_price
      FROM (
        SELECT
          id,
          product_availability_code,
          price,
          price_type,
          profit,
          competitor_price
        FROM product_tmp
        UNION
        SELECT
          id,
          ''out_of_stock''::product_availability_code,
          price,
          price_type,
          profit,
          competitor_price
        FROM product_update_register
        WHERE id NOT IN (SELECT id FROM product_tmp)
      ) AS data
      WHERE aggregation.product_' || update_geo_city_id || '.id = data.id
    ';

    IF update_all = FALSE THEN
      EXECUTE '
        UPDATE aggregation.product_update_register_' || update_geo_city_id || '
        SET is_updated = TRUE
        WHERE queued_at <= start_time
      ';
    END IF;
  END IF;

END
$BODY$
  LANGUAGE 'plpgsql' VOLATILE;
