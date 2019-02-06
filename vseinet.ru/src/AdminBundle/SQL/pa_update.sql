CREATE OR REPLACE FUNCTION pa_update(update_base_product_id int DEFAULT NULL, update_all bool DEFAULT FALSE)
  RETURNS void AS $BODY$
DECLARE
  update_start timestamp = NOW();
  cities CURSOR FOR
    SELECT gp.geo_city_id AS id
    FROM geo_point AS gp
    INNER JOIN representative AS r ON r.geo_point_id = gp.id
    WHERE r.is_active = TRUE AND r.has_retail = TRUE AND r.type IN ('our', 'torg', 'partner')
    GROUP BY gp.geo_city_id;
  city record;
BEGIN
  IF update_all = TRUE THEN
    CREATE OR REPLACE TEMP VIEW product_update_register_tmp AS SELECT * FROM aggregation.product_0;
    CREATE UNLOGGED TABLE aggregation.product_0_new (LIKE aggregation.product_0 INCLUDING DEFAULTS INCLUDING INDEXES);
    CREATE OR REPLACE TEMP VIEW product_0_tmp AS SELECT * FROM aggregation.product_0_new;
  ELSE
    CREATE TEMP TABLE product_update_register_tmp (LIKE public.product_update_register INCLUDING DEFAULTS INCLUDING INDEXES);
    CREATE TEMP TABLE product_0_tmp (LIKE aggregation.product_0 INCLUDING DEFAULTS INCLUDING INDEXES);

    IF update_base_product_id IS NULL THEN
      INSERT INTO product_update_register_tmp
      SELECT pur.*
      FROM public.product_update_register AS pur
      WHERE pur.queued_at <= update_start AND pur.is_updated = FALSE
      RETURNING pur.base_product_id;

      IF NOT found THEN
        RETURN;
      END IF;
    ELSE
      INSERT INTO product_update_register_tmp (base_product_id) VALUES (update_base_product_id);
    END IF;
  END IF;

  -- товары у поставщика
  INSERT INTO product_0_tmp (
    geo_city_id,
    base_product_id,
    product_availability_code,
    price,
    price_time,
    discount_amount,
    created_at,
    manual_price,
    manual_price_operated_at,
    manual_price_operated_by,
    rating
  )
  SELECT
    p.geo_city_id,
    p.base_product_id,
    (CASE WHEN bp.supplier_availability_code = 'in_transit' THEN 'awaiting' ELSE 'on_demand' END)::product_availability_code,
    p.price,
    p.price_time,
    p.discount_amount,
    p.created_at,
    p.manual_price,
    p.manual_price_operated_at,
    p.manual_price_operated_by,
    p.rating
  FROM aggregation.product_0 AS p
  INNER JOIN product_update_register_tmp AS pur ON pur.base_product_id = p.base_product_id
  INNER JOIN public.base_product AS bp ON bp.id = pur.base_product_id
  WHERE bp.supplier_availability_code IN ('available', 'in_transit', 'on_demand');

  -- свободные остатки, внутреннее перемещение и транзит от поставщика
  WITH
    data AS (
      SELECT grrc.base_product_id
      FROM public.goods_reserve_register_current AS grrc
      INNER JOIN product_update_register_tmp AS pur ON pur.base_product_id = grrc.base_product_id
      WHERE grrc.goods_condition_code = 'free'
      GROUP BY grrc.base_product_id
      HAVING SUM(grrc.delta) > 0
    ),
    updated AS (
      UPDATE product_0_tmp
      SET product_availability_code = 'on_demand'::product_availability_code
      FROM data
      WHERE product_0_tmp.base_product_id = data.base_product_id
      RETURNING data.base_product_id
    )
  INSERT INTO product_0_tmp (
    geo_city_id,
    base_product_id,
    product_availability_code,
    price,
    price_time,
    discount_amount,
    created_at,
    manual_price,
    manual_price_operated_at,
    manual_price_operated_by,
    rating
  )
  SELECT
    p.geo_city_id,
    p.base_product_id,
    'on_demand'::product_availability_code,
    p.price,
    p.price_time,
    p.discount_amount,
    p.created_at,
    p.manual_price,
    p.manual_price_operated_at,
    p.manual_price_operated_by,
    p.rating
  FROM data
  INNER JOIN aggregation.product_0 AS p ON p.base_product_id = data.base_product_id
  WHERE data.base_product_id NOT IN (SELECT base_product_id FROM updated);

  -- расчет цен
  WITH
    competitor_product AS (
      SELECT
        p.base_product_id,
        MIN(p2c.competitor_price) AS competitor_price
      FROM product_0_tmp AS p
      INNER JOIN product_to_competitor AS p2c ON p2c.base_product_id = p.base_product_id AND p2c.geo_city_id = 0
      INNER JOIN competitor AS c ON c.id = p2c.competitor_id
      WHERE c.is_active = TRUE AND p2c.competitor_price > 0
        AND
        CASE
          WHEN c.channel IN ('site', 'retail')
          THEN p2c.price_time + INTERVAL '7 day' >= NOW()
          ELSE p2c.price_time + INTERVAL '30 day' >= NOW()
        END
      GROUP BY p.base_product_id
    ),
    category_delivery AS (
      SELECT
        p.base_product_id,
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
      FROM product_0_tmp AS p
      WHERE p.base_product_id IN (SELECT base_product_id FROM competitor_product)
    ),
    standard_product AS (
      SELECT
        p.base_product_id,
        ROUND(bp.supplier_price * (
          SELECT
            tm.margin_percent
          FROM
            trade_margin AS tm
          INNER JOIN category_path AS cp ON cp.pid = tm.category_id
          WHERE tm.geo_city_id = 0 AND cp.id = bp.category_id AND bp.supplier_price BETWEEN tm.lower_limit AND tm.higher_limit
          ORDER BY cp.plevel DESC
          LIMIT 1
        ) / 100, -2) AS retail_price
      FROM product_0_tmp AS p
      INNER JOIN base_product AS bp ON bp.id = p.base_product_id
    )
  UPDATE product_0_tmp
  SET
    price = data.price,
    price_type = data.price_type::product_price_type,
    price_time = CASE WHEN product_0_tmp.price = data.price THEN product_0_tmp.price_time ELSE NOW() END,
    competitor_price = data.competitor_price,
    profit = data.price - data.purchase_price
  FROM (
    SELECT
      p.base_product_id,
      bp.supplier_price AS purchase_price,
      (CASE
        WHEN p.manual_price IS NOT NULL THEN p.manual_price
        WHEN cp.competitor_price IS NOT NULL AND sp.retail_price + cd.delivery_tax > cp.competitor_price
          AND cp.competitor_price > bp.supplier_price THEN cp.competitor_price * 0.3 + 0.7 * sp.retail_price - cd.delivery_tax
        WHEN bp.price_retail_min > sp.retail_price THEN bp.price_retail_min
        ELSE sp.retail_price
      END) AS price,
      (CASE
        WHEN p.manual_price IS NOT NULL THEN 'manual'
        WHEN cp.competitor_price IS NOT NULL AND sp.retail_price + cd.delivery_tax > cp.competitor_price
          AND cp.competitor_price > bp.supplier_price THEN 'compared'
        WHEN bp.price_retail_min > sp.retail_price THEN 'recommended'
        ELSE 'standard'
      END)::product_price_type AS price_type,
      cp.competitor_price
    FROM aggregation.product_0_new AS p
    INNER JOIN base_product AS bp ON bp.id = p.base_product_id
    LEFT JOIN category_delivery AS cd ON cd.base_product_id = p.base_product_id
    LEFT JOIN competitor_product AS cp ON cp.base_product_id = p.base_product_id
    LEFT JOIN standard_product AS sp ON sp.base_product_id = p.base_product_id
  ) AS data
  WHERE product_0_tmp.base_product_id = data.base_product_id;

  FOR city IN cities LOOP
    EXECUTE 'CREATE OR REPLACE TEMP VIEW product_x AS SELECT * FROM aggregation.product_' || city.id;

    IF update_all = TRUE THEN
      EXECUTE 'CREATE UNLOGGED TABLE aggregation.product_' || city.id || '_new (LIKE aggregation.product_0 INCLUDING DEFAULTS INCLUDING INDEXES)';
      EXECUTE 'CREATE OR REPLACE TEMP VIEW product_x_tmp AS SELECT * FROM aggregation.product_' || city.id || '_new';
    ELSE
      CREATE TEMP TABLE product_x_tmp (LIKE aggregation.product_0 INCLUDING DEFAULTS INCLUDING INDEXES);
    END IF;

    -- товары в наличии, в транзите и доступные под заказ
    WITH
      product_available AS (
        SELECT grrc.base_product_id
        FROM public.goods_reserve_register_current AS grrc
        INNER JOIN product_update_register_tmp AS pur ON pur.base_product_id = grrc.base_product_id
        WHERE grrc.goods_condition_code = 'free' AND grrc.geo_room_id NOT IN (
            SELECT gr.id
            FROM public.geo_room AS gr
            INNER JOIN public.geo_point AS gp ON gp.id = gr.geo_point_id
            WHERE gp.geo_city_id = city.id
          )
        GROUP BY grrc.base_product_id
        HAVING SUM(grrc.delta) > 0
      ),
      product_in_transit AS (
        SELECT grrc.base_product_id
        FROM public.goods_reserve_register_current AS grrc
        INNER JOIN product_update_register_tmp AS pur ON pur.base_product_id = grrc.base_product_id
        WHERE grrc.goods_condition_code = 'free' AND grrc.destination_geo_room_id IS NOT NULL
        GROUP BY grrc.base_product_id
        HAVING SUM(grrc.delta) > 0
      )
    INSERT INTO product_x_tmp (
      geo_city_id,
      base_product_id,
      product_availability_code,
      price,
      price_time,
      discount_amount,
      created_at,
      manual_price,
      manual_price_operated_at,
      manual_price_operated_by,
      ultimate_price,
      ultimate_price_operated_at,
      ultimate_price_operated_by,
      temporary_price,
      temporary_price_operated_at,
      temporary_price_operated_by,
      rating
    )
    SELECT
      city.id AS geo_city_id,
      p0.base_product_id,
      (CASE
        WHEN product_available.base_product_id IS NOT NULL THEN 'available'
        WHEN product_in_transit.base_product_id IS NOT NULL THEN 'in_transit'
        ELSE 'on_demand'
      END)::product_availability_code,
      COALESCE(p.price, p0.price) AS price,
      COALESCE(p.price_time, p0.price_time) AS price_time,
      COALESCE(p.discount_amount, p0.discount_amount) AS discount_amount,
      COALESCE(p.created_at, p0.created_at) AS created_at,
      COALESCE(p.manual_price, p0.manual_price) AS manual_price,
      COALESCE(p.manual_price_operated_at, p0.manual_price_operated_at) AS manual_price_operated_at,
      COALESCE(p.manual_price_operated_by, p0.manual_price_operated_by) AS manual_price_operated_by,
      p.ultimate_price,
      p.ultimate_price_operated_at,
      p.ultimate_price_operated_by,
      p.temporary_price,
      p.temporary_price_operated_at,
      p.temporary_price_operated_by,
      COALESCE(p.rating, p0.rating) AS rating
    FROM product_0_tmp AS p0
    LEFT OUTER JOIN product_x AS p ON p.base_product_id = p0.base_product_id
    LEFT OUTER JOIN product_available ON product_available.base_product_id = p.base_product_id
    LEFT OUTER JOIN product_in_transit ON product_in_transit.base_product_id = p.base_product_id
    WHERE p0.product_availability_code = 'on_demand';

    -- расчет цен
    WITH
      competitor_product AS (
        SELECT
          p.base_product_id,
          MIN(p2c.competitor_price) AS competitor_price
        FROM product_x_tmp AS p
        INNER JOIN product_to_competitor AS p2c ON p2c.base_product_id = p.base_product_id AND p2c.geo_city_id = city.id
        INNER JOIN competitor AS c ON c.id = p2c.competitor_id
        WHERE c.is_active = TRUE AND p2c.competitor_price > 0
          AND
          CASE
            WHEN c.channel IN ('site', 'retail')
            THEN p2c.price_time + INTERVAL '7 day' >= NOW()
            ELSE p2c.price_time + INTERVAL '30 day' >= NOW()
          END
        GROUP BY p.base_product_id
      ),
      category_delivery AS (
        SELECT
          p.base_product_id,
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
        FROM product_x_tmp AS p
        WHERE p.base_product_id IN (SELECT base_product_id FROM competitor_product)
      ),
      standard_product AS (
        SELECT
          p.base_product_id,
          ROUND(bp.supplier_price * (
            SELECT
              tm.margin_percent
            FROM
              trade_margin AS tm
            INNER JOIN category_path AS cp ON cp.pid = tm.category_id
            WHERE tm.geo_city_id IN (0, 1) AND cp.id = bp.category_id AND bp.supplier_price BETWEEN tm.lower_limit AND tm.higher_limit
            ORDER BY tm.geo_city_id DESC, cp.plevel DESC
            LIMIT 1
          ) / 100, -2) AS retail_price
        FROM product_x_tmp AS p
        INNER JOIN base_product AS bp ON bp.id = p.base_product_id
      )
    UPDATE product_x_tmp
    SET
      price = data.price,
      price_type = data.price_type::product_price_type,
      price_time = CASE WHEN product_x_tmp.price = data.price THEN product_x_tmp.price_time ELSE NOW() END,
      competitor_price = data.competitor_price,
      profit = data.price - data.purchase_price
    FROM (
      SELECT
        p.base_product_id,
        bp.supplier_price AS purchase_price,
        (CASE
          WHEN p.temporary_price IS NOT NULL AND p.product_availability_code = 'available' THEN p.temporary_price
          WHEN p.ultimate_price IS NOT NULL AND p.product_availability_code = 'available' THEN p.ultimate_price
          WHEN p.manual_price IS NOT NULL THEN p.manual_price
          WHEN cp.competitor_price IS NOT NULL AND sp.retail_price + cd.delivery_tax > cp.competitor_price
            AND cp.competitor_price > bp.supplier_price THEN cp.competitor_price * 0.3 + 0.7 * sp.retail_price - cd.delivery_tax
          WHEN bp.price_retail_min > sp.retail_price THEN bp.price_retail_min
          ELSE sp.retail_price
        END) AS price,
        (CASE
          WHEN p.temporary_price IS NOT NULL AND p.product_availability_code = 'available' THEN 'temporary'
          WHEN p.ultimate_price IS NOT NULL AND p.product_availability_code = 'available' THEN 'ultimate'
          WHEN p.manual_price IS NOT NULL THEN 'manual'
          WHEN cp.competitor_price IS NOT NULL AND sp.retail_price + cd.delivery_tax > cp.competitor_price
            AND cp.competitor_price > bp.supplier_price THEN 'compared'
          WHEN bp.price_retail_min > sp.retail_price THEN 'recommended'
          ELSE 'standard'
        END)::product_price_type AS price_type,
        cp.competitor_price
      FROM product_x_tmp AS p
      INNER JOIN base_product AS bp ON bp.id = p.base_product_id
      LEFT JOIN category_delivery AS cd ON cd.base_product_id = p.base_product_id
      LEFT JOIN competitor_product AS cp ON cp.base_product_id = p.base_product_id
      LEFT JOIN standard_product AS sp ON sp.base_product_id = p.base_product_id
    ) AS data
    WHERE product_x_tmp.base_product_id = data.base_product_id;

    DELETE FROM product_x_tmp
    WHERE base_product_id IN (
      SELECT p.base_product_id
      FROM product_x_tmp AS p
      INNER JOIN aggregation.product_0_new AS p0 ON p0.base_product_id = p.base_product_id
      WHERE p.product_availability_code = 'on_demand' AND p.price = p0.price
    );

    IF update_all = FALSE THEN
      DELETE FROM product_x WHERE base_product_id IN (SELECT base_product_id FROM product_update_register_tmp);
      INSERT INTO product_x SELECT * FROM product_x_tmp;
      DROP TABLE product_x_tmp;
    ELSE
      EXECUTE 'DROP TABLE aggregation.product_' || city.id || ' CASCADE';
      EXECUTE 'ALTER TABLE aggregation.product_' || city.id || '_new RENAME TO product_' || city.id;
      EXECUTE 'ALTER TABLE aggregation.product_' || city.id || ' SET LOGGED';
      EXECUTE 'ALTER TABLE aggregation.product_' || city.id || ' INHERIT public.product';
      EXECUTE '
        ALTER TABLE aggregation.product_' || city.id || '
        ADD CONSTRAINT product_' || city.id || '_geo_city_id_check
        CHECK (geo_city_id = ' || city.id || ')
      ';
      EXECUTE '
        ALTER TABLE aggregation.product_' || city.id || '
        ADD CONSTRAINT product_' || city.id || '_base_product_id_fkey
        FOREIGN KEY (base_product_id)
        REFERENCES public.base_product (id)
        ON UPDATE NO ACTION
        ON DELETE CASCADE
      ';
      EXECUTE '
        ALTER TABLE aggregation.product_' || city.id || '
        ADD CONSTRAINT product_' || city.id || '_geo_city_id_fkey
        FOREIGN KEY (geo_city_id)
        REFERENCES public.geo_city (id)
        ON UPDATE NO ACTION
        ON DELETE CASCADE
      ';
      EXECUTE '
        ALTER TABLE aggregation.product_' || city.id || '
        ADD CONSTRAINT product_' || city.id || '_manual_price_operated_by_fkey
        FOREIGN KEY (manual_price_operated_by)
        REFERENCES public."user" (id)
        ON UPDATE CASCADE
        ON DELETE RESTRICT
      ';
      EXECUTE '
        ALTER TABLE aggregation.product_' || city.id || '
        ADD CONSTRAINT product_' || city.id || '_temporary_price_operated_by_fkey
        FOREIGN KEY (temporary_price_operated_by)
        REFERENCES public."user" (id)
        ON UPDATE CASCADE
        ON DELETE RESTRICT
      ';
      EXECUTE '
        ALTER TABLE aggregation.product_' || city.id || '
        ADD CONSTRAINT product_' || city.id || '_ultimate_price_operated_by_fkey
        FOREIGN KEY (ultimate_price_operated_by)
        REFERENCES public."user" (id)
        ON UPDATE CASCADE
        ON DELETE RESTRICT
      ';
    END IF;
  END LOOP;

  IF update_all = FALSE THEN
    DELETE FROM aggregation.product_0 WHERE base_product_id IN (SELECT base_product_id FROM aggregation.product_0_new);
    UPDATE aggregation.product_0
    SET product_availability_code = 'out_of_stock'::product_availability_code
    WHERE base_product_id IN (SELECT base_product_id FROM product_update_register_tmp);
    INSERT INTO aggregation.product_0 SELECT * FROM aggregation.product_0_new;
    UPDATE public.product_update_register
    SET is_updated = TRUE
    WHERE queued_at <= update_start;
  ELSE
    INSERT INTO aggregation.product_0_new (
      geo_city_id,
      base_product_id,
      product_availability_code,
      price,
      price_type,
      price_time,
      discount_amount,
      created_at,
      manual_price,
      manual_price_operated_at,
      manual_price_operated_by,
      competitor_price,
      rating,
      profit
    )
    SELECT
      p.geo_city_id,
      p.base_product_id,
      'out_of_stock'::product_availability_code,
      p.price,
      p.price_type,
      p.price_time,
      p.discount_amount,
      p.created_at,
      p.manual_price,
      p.manual_price_operated_at,
      p.manual_price_operated_by,
      p.competitor_price,
      p.rating,
      p.profit
    FROM aggregation.product_0 AS p
    WHERE p.base_product_id NOT IN (SELECT base_product_id FROM aggregation.product_0_new);

    DROP TABLE aggregation.product_0 CASCADE;
    ALTER TABLE aggregation.product_0_new RENAME TO product_0;
    ALTER TABLE aggregation.product_0 SET LOGGED;

    ALTER TABLE aggregation.product_0 INHERIT public.product;
    ALTER TABLE aggregation.product_0
    ADD CONSTRAINT product_0_geo_city_id_check
    CHECK (geo_city_id = 0);

    ALTER TABLE aggregation.product_0
    ADD CONSTRAINT product_0_base_product_id_fkey
    FOREIGN KEY (base_product_id)
    REFERENCES public.base_product (id)
    ON UPDATE NO ACTION
    ON DELETE CASCADE;

    ALTER TABLE aggregation.product_0
    ADD CONSTRAINT product_0_geo_city_id_fkey
    FOREIGN KEY (geo_city_id)
    REFERENCES public.geo_city (id)
    ON UPDATE NO ACTION
    ON DELETE CASCADE;

    ALTER TABLE aggregation.product_0
    ADD CONSTRAINT product_0_manual_price_operated_by_fkey
    FOREIGN KEY (manual_price_operated_by)
    REFERENCES public."user" (id)
    ON UPDATE CASCADE
    ON DELETE RESTRICT;
  END IF;

END
$BODY$
  LANGUAGE 'plpgsql' VOLATILE;