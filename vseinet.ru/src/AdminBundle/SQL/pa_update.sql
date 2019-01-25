

CREATE OR REPLACE FUNCTION pa_update(geo_city_id int, id int8 DEFAULT NULL, is_all bool DEFAULT FALSE)
  RETURNS void AS $BODY$
DECLARE
  partition_name text = 'product_' || geo_city_id;
  partition_update_register_name text = 'product_update_register_' || geo_city_id;
  partition_update_function_name text = 'product_update_' || geo_city_id || '_all()';
  partition_update_register_join text = '';
  select_ids_sql text = '';
  update_ids_sql text = '';

  start_time timestamp = NOW();
BEGIN
  IF is_all THEN
    EXECUTE '
      CREATE TEMP VIEW product_update_register (product_id, base_product_id)
      AS SELECT id, base_product_id FROM aggregation.product_' || geo_city_id;
  ELSE
    CREATE TEMP TABLE product_update_register (
      product_id int8 NOT NULL,
      base_product_id int8 NOT NULL,
      CONSTRAINT product_update_register_pkey PRIMARY KEY (product_id)
    );
    CREATE INDEX product_update_register_base_product_id_idx ON product_update_register USING btree (base_product_id ASC NULLS LAST);

    IF id IS NULL THEN
      EXECUTE '
        INSERT INTO product_update_register (product_id, base_product_id)
        SELECT pur.id, b.base_product_id
        FROM aggregation.product_update_register_' || geo_city_id || ' AS pur
        INNER JOIN aggregation.product_ ' || geo_city_id || ' AS p ON p.id = pur.id
        WHERE queued_at <= $1 AND is_updated = false
        GROUP BY pur.id'
      USING start_time;

      SELECT product_id INTO id FROM product_update_register LIMIT 1;
      IF NOT found THEN
        RETURN;
      END IF;
    ELSE
      EXECUTE '
        INSERT INTO product_update_register (product_id, base_product_id)
        SELECT p.id, p.base_product_id
        FROM aggregation.product_' || geo_city_id || ' AS p
        WHERE p.id = $1'
      USING id;
    END IF;
  END IF;

  -- резервы
  CREATE TEMP TABLE product_tmp_reserve (
    id int8 NOT NULL,
    has_free_reserve bool DEFAULT FALSE,          -- available
    has_other_reserve bool DEFAULT FALSE,         -- on_demand
    has_free_supplier bool DEFAULT FALSE,         -- on_demand
    has_inner_transit bool DEFAULT FALSE,         -- in_transit
    has_from_supplier_transit bool DEFAULT FALSE, -- in_transit
    has_to_supplier_transit bool DEFAULT FALSE,   -- awating
    CONSTRAINT product_tmp_reserve_pkey PRIMARY KEY (id)
  );

  -- свободные остатки - available
  WITH
    data (id, has_free_reserve) AS (
      SELECT p.id, TRUE
      FROM public.goods_reserve_register_current AS grrc
      INNER JOIN product_update_register AS p ON p.base_product_id = grrc.base_product_id
      WHERE grrc.goods_condition_code = 'free' AND grrc.geo_room_id IN (
          SELECT gr.id
          FROM public.geo_room AS gr
          INNER JOIN public.geo_point AS gp ON gp.id = gr.geo_point_id
          WHERE gp.geo_city_id = geo_city_id
        )
      GROUP BY p.id
      HAVING SUM(grrc.delta) > 0
    ),
    updated AS (
      UPDATE product_tmp_reserve
      SET has_free_reserve = data.has_free_reserve
      FROM data
      WHERE product_tmp_reserve.id = data.id
      RETURNING data.id
    )
  INSERT INTO product_tmp_reserve (id, has_free_reserve)
  SELECT id, has_free_reserve
  FROM data
  WHERE id NOT IN (SELECT id FROM updated);

  -- свободные остатки в других городах - on_demand
  WITH
    data (id, has_other_reserve) AS (
      SELECT p.id, TRUE
      FROM public.goods_reserve_register_current AS grrc
      INNER JOIN product_update_register AS p ON p.base_product_id = grrc.base_product_id
      WHERE grrc.goods_condition_code = 'free' AND grrc.geo_room_id NOT IN (
          SELECT gr.id
          FROM public.geo_room AS gr
          INNER JOIN public.geo_point AS gp ON gp.id = gr.geo_point_id
          WHERE gp.geo_city_id = geo_city_id
        )
      GROUP BY p.id
      HAVING SUM(grrc.delta) > 0
    ),
    updated AS (
      UPDATE product_tmp_reserve
      SET has_other_reserve = data.has_other_reserve
      FROM data
      WHERE product_tmp_reserve.id = data.id
      RETURNING data.id
    )
  INSERT INTO product_tmp_reserve (id, has_other_reserve)
  SELECT id, has_free_reserve
  FROM data
  WHERE id NOT IN (SELECT id FROM updated);

  -- в наличии у поставщика - on_demand
  WITH
    data (id, has_free_supplier) AS (
      SELECT p.id, TRUE
      FROM product_update_register AS p
      INNER JOIN public.base_product AS bp ON bp.id = p.base_product_id
      WHERE bp.supplier_availability_code = 'available'
    ),
    updated AS (
      UPDATE product_tmp_reserve
      SET has_free_supplier = data.has_free_supplier
      FROM data
      WHERE product_tmp_reserve.id = data.id
      RETURNING data.id
    )
  INSERT INTO product_tmp_reserve (id, has_free_supplier)
  SELECT id, has_free_reserve
  FROM data
  WHERE id NOT IN (SELECT id FROM updated);

  -- внутреннее перемещение - in_transit
  WITH
    data (id, has_inner_transit) AS (
      SELECT p.id, TRUE
      FROM public.goods_reserve_register_current AS grrc
      INNER JOIN product_update_register AS p ON p.base_product_id = grrc.base_product_id
      WHERE grrc.goods_condition_code = 'free' AND grrc.destination_geo_room_id IS NOT NULL AND grrc.goods_release_id IS NOT NULL
      GROUP BY p.id
      HAVING SUM(grrc.delta) > 0
    ),
    updated AS (
      UPDATE product_tmp_reserve
      SET has_inner_transit = data.has_inner_transit
      FROM data
      WHERE product_tmp_reserve.id = data.id
      RETURNING data.id
    )
  INSERT INTO product_tmp_reserve (id, has_inner_transit)
  SELECT id, has_inner_transit
  FROM data
  WHERE id NOT IN (SELECT id FROM updated);

  -- транзит от поставщика - in_transit
  WITH
    data (id, has_supplier_transit) AS (
      SELECT p.id, TRUE
      FROM public.goods_reserve_register_current AS grrc
      INNER JOIN product_update_register AS p ON p.base_product_id = grrc.base_product_id
      WHERE grrc.goods_condition_code = 'free' AND grrc.destination_geo_room_id IS NOT NULL AND grrc.goods_release_id IS NULL
      GROUP BY p.id
      HAVING SUM(grrc.delta) > 0
    ),
    updated AS (
      UPDATE product_tmp_reserve
      SET has_supplier_transit = data.has_supplier_transit
      FROM data
      WHERE product_tmp_reserve.id = data.id
      RETURNING data.id
    )
  INSERT INTO product_tmp_reserve (id, has_supplier_transit)
  SELECT id, has_supplier_transit
  FROM data
  WHERE id NOT IN (SELECT id FROM updated);

  -- временные таблицы
  CREATE TEMP TABLE product_tmp (LIKE public.product);
  ALTER TABLE product_tmp ADD CONSTRAINT product_tmp_pkey PRIMARY KEY (id);
  CREATE INDEX product_tmp_base_product_id_idx ON product_tmp USING btree (base_product_id ASC NULLS LAST);

  CREATE TEMP TABLE product_tmp_available () INHERITS (product_tmp);
  ALTER TABLE product_tmp_available ADD CONSTRAINT product_tmp_available_pkey PRIMARY KEY (id);
  CREATE INDEX product_tmp_available_base_product_id_idx ON product_tmp_available USING btree (base_product_id ASC NULLS LAST);

  CREATE TEMP TABLE product_tmp_on_demand () INHERITS (product_tmp);
  ALTER TABLE product_tmp_on_demand ADD CONSTRAINT product_tmp_on_demand_pkey PRIMARY KEY (id);
  CREATE INDEX product_tmp_on_demand_base_product_id_idx ON product_tmp_on_demand USING btree (base_product_id ASC NULLS LAST);

  -- товары в наличии, включают товары на перемещении и в транзите к нам от поставщика
  INSERT INTO product_tmp_available (
    id,
    base_product_id,
    product_availability_code,
    price,
    price_type,
    manual_price,
    ultimate_price,
    competitor_price,
    temporary_price,
    profit
  )
  SELECT
    p.id,
    p.base_product_id,
    (CASE WHEN p.has_free_reserve = TRUE THEN 'available' ELSE 'in_transit' END)::product_availability_code,
    p.price,
    p.price_type,
    p.manual_price,
    p.ultimate_price,
    p.competitor_price,
    p.temporary_price,
    p.profit
  FROM product_tmp_reserve AS p



  SELECT p.*
  FROM public.goods_reserve_register_current AS grrc
  INNER JOIN product_update_register AS pur ON pur.base_product_id = grrc.base_product_id
  INNER JOIN public.geo_room AS gr ON gr.id = grrc.geo_room_id
  INNER JOIN public.geo_point AS gp ON gp.id = gr.geo_point_id
  INNER JOIN aggregation.product_1 AS p ON p.base_product_id = grrc.base_product_id AND p.geo_city_id = gp.geo_city_id
  WHERE grrc.goods_condition_code = 'free'
  GROUP BY grrc.base_product_id
  HAVING SUM(grrc.delta) > 0
END
$BODY$
  LANGUAGE 'plpgsql' VOLATILE;
