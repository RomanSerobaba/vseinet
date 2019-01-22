CREATE OR REPLACE FUNCTION public.product_create_update_function(geo_city_id int, is_all bool DEFAULT false)
  RETURNS void AS $BODY$
DECLARE
  partition_name text = 'product_' || geo_city_id;
  partition_update_register_name text;
  partition_update_function_name text;
  select_ids_sql text = '';
  update_ids_sql text = '';
  outro
BEGIN
  IF is_all THEN
    partition_update_register_name = 'product_' || geo_city_id;
    partition_update_function_name = 'product_update_all_' || geo_city_id || '()';
  ELSE
    partition_update_register_name = 'product_update_regsiter';
    partition_update_function_name = 'product_update_' || geo_city_id || '(id int DEFAULT NULL)'
    select_ids_sql = '
      CREATE TEMP TABLE product_update_register (
        id bigint NOT NULL,
        CONSTRAINT product_update_register_pkey PRIMARY KEY (id)
      )
      ON COMMIT DROP;
      IF id IS NULL THEN
        INSERT INTO product_update_register (id)
        SELECT id FROM aggregation.product_update_register_' || geo_city_id || ';
        SELECT id INTO id FROM product_update_register LIMIT 1;
        IF NOT found THEN
          RETURN NULL;
        END IF;
      ELSE
        INSERT INTO product_update_register (id) VALUES($1) USING id;
      END IF;
    ';
    update_ids_sql = '
      UPDATE aggregation.product_update_register_' || geo_city_id || '
      SET is_updated = true
      WHERE id = (SELECT id FROM product_update_register);
    ';
  END IF;

  EXECUTE '
    CREATE OR REPLACE FUNCTION aggregation.' || partition_update_function_name || '
      RETURNS void AS $PRODUCT_UPDATE$
    BEGIN
      ' || select_ids_sql || '

      -- резервы
      CREATE TEMP TABLE product_tmp_reserve (
        product_id INTEGER NOT NULL,
        free_reserve BOOL DEFAULT FALSE,
        inner_transit BOOL DEFAULT FALSE,
        supplier_transit BOOL DEFAULT FALSE,
        CONSTRAINT product_tmp_reserve_pkey PRIMARY KEY (product_id)
      )
      ON COMMIT DROP;

      -- свободные остатки
      WITH
        data (product_id, free_reserve) AS (
          SELECT p.id, true
          FROM public.goods_reserve_register_current AS grr
          INNER JOIN public.geo_room AS gr ON gr.id = grr.geo_room_id
          INNER JOIN public.geo_point AS gp ON gp.id = gr.geo_point_id
          INNER JOIN aggregation.' || partition_name || ' AS p ON p.base_product_id = grr.base_product_id
          INNER JOIN aggregation.' || partition_update_register_name || ' AS pu ON pu.id = p.id
          WHERE grr.goods_condition_code = ''free'' AND gp.geo_city_id = p.geo_city_id
          GROUP BY p.id
          HAVING SUM(grr.delta) > 0
        ),
        updated AS (
          UPDATE product_tmp_reserve
          SET free_reserve = data.free_reserve
          FROM data
          WHERE aggregation.product_tmp_reserve.product_id = data.product_id
          RETURNING data.product_id
        )
      INSERT INTO product_tmp_reserve (product_id, free_reserve)
      SELECT product_id, free_reserve
      FROM data
      WHERE product_id NOT IN (SELECT product_id FROM updated);

      -- внутреннее перемещение
      WITH
        data (product_id, inner_transit) AS (
          SELECT p.id, TRUE
          FROM public.goods_reserve_register_current AS grr
          INNER JOIN public.goods_release_doc AS gre ON gre.number = grr.goods_release_id
          INNER JOIN public.geo_room AS gr ON gr.id = gre.destination_room_id
          INNER JOIN public.geo_point AS gp ON gp.id = gr.geo_point_id
          INNER JOIN aggregation.' || partition_name || ' AS p ON p.base_product_id = grr.base_product_id
          INNER JOIN aggregation.' || partition_update_register_name || ' AS pu ON pu.id = p.id
          WHERE grr.goods_condition_code = ''free'' AND gp.geo_city_id = p.geo_city_id
          GROUP BY p.id
          HAVING SUM(grr.delta) > 0
        ),
        updated AS (
          UPDATE product_tmp_reserve
          SET inner_transit = data.inner_transit
          FROM data
          WHERE product_tmp_reserve.product_id = data.product_id
          RETURNING data.product_id
        )
      INSERT INTO product_tmp_reserve (product_id, inner_transit)
      SELECT product_id, inner_transit
      FROM data
      WHERE product_id NOT IN (SELECT product_id FROM updated);

      -- транзит от поставщика
      WITH
        data (product_id, supplier_transit) AS (
          SELECT p.id, TRUE
          FROM public.goods_reserve_register_current AS grr
          INNER JOIN public.supply_item AS si ON si.id = grr.supply_item_id
          INNER JOIN public.supply_doc AS s ON s.did = si.parent_did
          INNER JOIN public.geo_room AS gr ON gr.id = s.destination_room_id
          INNER JOIN public.geo_point AS gp ON gp.id = gr.geo_point_id
          INNER JOIN aggregation.' || partition_name || ' AS p ON p.base_product_id = grr.base_product_id
          INNER JOIN aggregation.' || partition_update_register_name || ' AS pu ON pu.id = p.id
          WHERE grr.goods_condition_code = ''free'' AND gp.geo_city_id = p.geo_city_id
          GROUP BY p.id
          HAVING SUM(grr.delta) > 0
        ),
        updated AS (
          UPDATE product_tmp_reserve
          SET supplier_transit = data.supplier_transit
          FROM data
          WHERE product_tmp_reserve.product_id = data.product_id
          RETURNING data.product_id
        )
      INSERT INTO product_tmp_reserve (product_id, supplier_transit)
      SELECT product_id, supplier_transit
      FROM data
      WHERE product_id NOT IN (SELECT product_id FROM updated);

      CREATE TEMP TABLE product_tmp AS SELECT * FROM public.product WITH NO DATA ON COMMIT DROP;
      ALTER TABLE product_tmp ADD CONSTRAINT product_tmp_pkey PRIMARY KEY (id);

      CREATE TEMP TABLE product_tmp_available () WITH (OIDS = true) ON COMMIT DROP INHERITS (product_tmp);
      ALTER TABLE product_tmp_available ADD CONSTRAINT product_tmp_available_pkey PRIMARY KEY (id);
      CREATE INDEX product_tmp_available_base_product_id_idx ON product_tmp_available USING btree (
        base_product_id ASC NULLS LAST
      );

      CREATE TEMP TABLE product_tmp_on_demand () WITH (OIDS = true) ON COMMIT DROP INHERITS (product_tmp);
      ALTER TABLE product_tmp_on_demand ADD CONSTRAINT product_tmp_on_demand_pkey PRIMARY KEY (id);
      CREATE INDEX product_tmp_on_demand_base_product_id_idx ON product_tmp_on_demand USING btree (
        base_product_id ASC NULLS LAST
      );

      CREATE TEMP TABLE product_tmp_out_of_stock () WITH (OIDS = true) ON COMMIT DROP INHERITS (product_tmp);
      ALTER TABLE product_tmp_out_of_stock ADD CONSTRAINT product_tmp_out_of_stock_pkey PRIMARY KEY (id);

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
        (CASE WHEN product_tmp_reserve.free_reserve = TRUE THEN ''available'' ELSE ''in_transit'' END)::product_availability_code,
        p.price,
        p.price_type,
        p.manual_price,
        p.ultimate_price,
        p.competitor_price,
        p.temporary_price,
        p.profit
      FROM aggregation.' || partition_name || ' AS p
      INNER JOIN product_tmp_reserve ON product_tmp_reserve.product_id = p.id;

      -- товары в наличии для других городов (доступны под заказ)
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
        ''on_demand''::product_availability_code,
        p.price,
        p.price_type,
        p.manual_price,
        p.ultimate_price,
        p.competitor_price,
        p.temporary_price,
        p.profit
      FROM aggregation.' || partition_name || ' AS p
      INNER JOIN product_tmp_available on product_tmp_available.base_product_id = p.base_product_id
      ON CONFLICT DO NOTHING;

      -- товары под заказ у поставщика, а также в тразите у постащика (ожидаются)
      INSERT INTO product_tmp_on_demand (
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
        (CASE WHEN base_product.supplier_availability_code = ''in_transit'' THEN ''awaiting'' ELSE ''on_demand'' END)::product_availability_code,
        p.price,
        p.price_type,
        p.manual_price,
        p.ultimate_price,
        p.competitor_price,
        p.temporary_price,
        p.profit
      FROM aggregation.' || partition_name || ' AS p
      INNER JOIN public.base_product AS bp ON bp.id = p.base_product_id
      LEFT OUTER JOIN product_tmp ON product_tmp.id = p.id
      WHERE bp.supplier_availability_code IN (''available'', ''on_demand'', ''in_transit'') AND product_tmp.id IS NULL;

      -- нет в наличии
      INSERT INTO product_tmp_out_of_stock (
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
        ''out_of_stock''::product_availability_code,
        p.price,
        p.price_type,
        p.manual_price,
        p.ultimate_price,
        p.competitor_price,
        p.temporary_price,
        p.profit
      FROM aggregation.' || partition_name || ' AS p
      LEFT OUTER JOIN product_tmp ON product_tmp.id = p.id
      WHERE product_tmp.id IS NULL;

      -- расчет цен товаров в наличии
      UPDATE product_tmp_available
      SET
        price = data.price,
        price_type = data.price_type::product_price_type,
        profit = data.price - data.purchase_price
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
            FROM product_tmp_available AS p
          ),
          competitor_product AS (
            SELECT
              p.id AS product_id,
              MIN(p2c.competitor_price) AS competitor_price
            FROM product_tmp_available AS p
            INNER JOIN product_to_competitor AS p2c ON p2c.product_id = p.id
            INNER JOIN competitor AS c ON c.id = p2c.competitor_id
            WHERE c.is_active = true AND p2c.competitor_price > 0
              AND
              CASE
                WHEN c.channel IN (''site'', ''retail'')
                THEN p2c.price_time + INTERVAL ''7 day'' >= NOW()
                ELSE p2c.price_time + INTERVAL ''30 day'' >= NOW()
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
                INNER JOIN category_path cp ON cp.pid = tm.category_id
                WHERE cp.id = bp.category_id AND bp.supplier_price BETWEEN tm.lower_limit AND tm.higher_limit
                ORDER BY cp.plevel DESC
                LIMIT 1
              ) / 100, -2) AS retail_price
            FROM product_tmp_available AS p
            INNER JOIN base_product bp ON bp.id = p.base_product_id
          )
        SELECT
          p.id AS product_id,
          bp.supplier_price AS purchase_price,
          CASE
            WHEN p.temporary_price > 0 AND p.product_availability_code = ''available'' THEN p.temporary_price
            WHEN p.ultimate_price > 0 AND p.product_availability_code = ''available'' THEN p.ultimate_price
            WHEN p.manual_price > 0 THEN p.manual_price
            WHEN cp.competitor_price > 0 AND sp.retail_price + cd.delivery_tax > cp.competitor_price
              AND cp.competitor_price > bp.supplier_price THEN cp.competitor_price * 0.3 + 0.7 * sp.retail_price - cd.delivery_tax
            WHEN bp.price_retail_min > sp.retail_price THEN bp.price_retail_min
            ELSE sp.retail_price
          END AS price,
          CASE
            WHEN p.temporary_price > 0 AND p.product_availability_code = ''available'' THEN ''temporary''
            WHEN p.ultimate_price > 0 AND p.product_availability_code = ''available'' THEN ''ultimate''
            WHEN p.manual_price > 0 THEN ''manual''
            WHEN cp.competitor_price > 0 AND sp.retail_price + cd.delivery_tax > cp.competitor_price
              AND cp.competitor_price > bp.supplier_price THEN ''compared''
            WHEN bp.price_retail_min > sp.retail_price THEN ''recommended''
            ELSE ''standard''
          END AS price_type
        FROM product_tmp_available AS p
        INNER JOIN base_product AS bp ON bp.id = p.base_product_id
        LEFT JOIN category_delivery AS cd ON cd.product_id = p.id
        LEFT JOIN competitor_product AS cp ON cp.product_id = p.id
        LEFT JOIN standard_product AS sp ON sp.product_id = p.id
      ) AS data
      WHERE product_tmp_available.id = data.product_id;

      -- расчет цен товаров под заказ
      UPDATE product_tmp_on_demand
      SET
        price = data.price,
        price_type = data.price_type::product_price_type,
        profit = data.price - data.purchase_price
      FROM (
        WITH
          category_delivery AS (
            SELECT
              p.id AS product_id,
              COALESCE(
                (SELECT
                  c.delivery_tax
                FROM base_product AS bp
                INNER JOIN category_path AS cp ON cp.id = bp.category_id
                INNER JOIN category AS c ON c.id = cp.pid
                WHERE c.delivery_tax > 0 AND bp.id = p.base_product_id
                ORDER BY cp.plevel DESC
                LIMIT 1), 0
              ) AS delivery_tax
            FROM product_tmp_on_demand AS p
          ),
          competitor_product AS (
            SELECT
              p.id AS product_id,
              MIN(p2c.competitor_price) AS competitor_price
            FROM product_tmp_on_demand AS p
            INNER JOIN product_to_competitor AS p2c ON p2c.product_id = p.id
            INNER JOIN competitor AS c ON c.id = p2c.competitor_id
            WHERE c.is_active = true AND p2c.competitor_price > 0
              AND
              CASE
                WHEN c.channel IN (''site'', ''retail'')
                THEN p2c.price_time + INTERVAL ''7 day'' >= NOW()
                ELSE p2c.price_time + INTERVAL ''30 day'' >= NOW()
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
            FROM product_tmp_on_demand AS p
            INNER JOIN base_product AS bp ON bp.id = p.base_product_id
          )
        SELECT
          p.id AS product_id,
          bp.supplier_price AS purchase_price,
          CASE
            WHEN p.manual_price > 0 THEN p.manual_price
            WHEN cp.competitor_price > 0 AND sp.retail_price + cd.delivery_tax > cp.competitor_price
              AND cp.competitor_price > bp.supplier_price THEN cp.competitor_price * 0.3 + 0.7 * sp.retail_price - cd.delivery_tax
            WHEN bp.price_retail_min > sp.retail_price THEN bp.price_retail_min
            ELSE sp.retail_price
          END AS price,
          CASE
            WHEN p.manual_price > 0 THEN ''manual''
            WHEN cp.competitor_price > 0 AND sp.retail_price + cd.delivery_tax > cp.competitor_price
              AND cp.competitor_price > bp.supplier_price THEN ''compared''
            WHEN bp.price_retail_min > sp.retail_price THEN ''recommended''
            ELSE ''standard''
          END AS price_type
        FROM product_tmp_on_demand AS p
        INNER JOIN base_product AS bp ON bp.id = p.base_product_id
        LEFT JOIN category_delivery AS cd ON cd.product_id = p.id
        LEFT JOIN competitor_product AS cp ON cp.product_id = p.id
        LEFT JOIN standard_product AS sp ON sp.product_id = p.id
      ) AS data
      WHERE product_tmp_on_demand.id = data.product_id;

      WITH
        data AS (
          SELECT
            pt.id,
            pt.product_availability_code,
            pt.price,
            pt.price_type,
            pt.profit
          FROM product_tmp AS pt
          EXCEPT
          SELECT
            p.id,
            p.product_availability_code,
            p.price,
            p.price_type,
            p.profit
          FROM aggregation.' || partition_name || ' AS p
        )
      UPDATE aggregation.' || partition_name || ' AS p
      SET
        p.product_availability_code = data.product_availability_code,
        p.price = data.price,
        p.price_type = data.price_type,
        p.price_time = CASE p.price = data.price THEN p.price_time ELSE NOW() END,
        p.profit = data.profit
      FROM data;

      ' || update_ids_sql || '

    END
    $BODY$
      LANGUAGE ''plpgsql'' VOLATILE;

        END
        $PRODUCT_UPDATE$
          LANGUAGE ''plpgsql'' VOLATILE;
  ';
END
$BODY$
  LANGUAGE 'plpgsql' VOLATILE;