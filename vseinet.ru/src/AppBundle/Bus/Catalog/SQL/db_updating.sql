-- резервы

DROP TABLE IF EXISTS product_tmp_reserve;
CREATE UNLOGGED TABLE product_tmp_reserve (
  product_id INTEGER NOT NULL,
  free_reserve BOOL DEFAULT FALSE,
  inner_transit BOOL DEFAULT FALSE,
  supplier_transit BOOL DEFAULT FALSE
);
ALTER TABLE product_tmp_reserve ADD CONSTRAINT product_tmp_reserve_pkey PRIMARY KEY (product_id);

-- свободные остатки

WITH
    data (product_id, free_reserve) AS (
        SELECT p.id, TRUE
        FROM goods_reserve_register_current AS grr
        INNER JOIN geo_room AS gr ON gr.id = grr.geo_room_id
        INNER JOIN geo_point AS gp ON gp.id = gr.geo_point_id
        INNER JOIN product AS p ON p.base_product_id = grr.base_product_id
        WHERE grr.goods_condition_code = 'free' AND gp.geo_city_id = p.geo_city_id
        GROUP BY p.id
        HAVING SUM(grr.delta) > 0
    ),
    updated AS (
        UPDATE product_tmp_reserve
        SET free_reserve = data.free_reserve
        FROM data
        WHERE product_tmp_reserve.product_id = data.product_id
        RETURNING data.product_id
    )
INSERT INTO product_tmp_reserve (product_id, free_reserve)
SELECT product_id, free_reserve
FROM data
WHERE product_id NOT IN (SELECT product_id FROM updated);

WITH
    data (product_id, free_reserve) AS (
        SELECT p.id, TRUE
        FROM goods_reserve_register_current AS grr
        INNER JOIN geo_room AS gr ON gr.id = grr.geo_room_id
        INNER JOIN geo_point AS gp ON gp.id = gr.geo_point_id
        INNER JOIN product AS p ON p.base_product_id = grr.base_product_id
        WHERE grr.goods_condition_code = 'free' AND p.geo_city_id IS NULL
        GROUP BY p.id
        HAVING SUM(grr.delta) > 0
    ),
    updated AS (
        UPDATE product_tmp_reserve
        SET free_reserve = data.free_reserve
        FROM data
        WHERE product_tmp_reserve.product_id = data.product_id
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
        FROM goods_reserve_register_current AS grr
        INNER JOIN goods_release_doc AS gre ON gre.number = grr.goods_release_id
        INNER JOIN geo_room AS gr ON gr.id = gre.destination_room_id
        INNER JOIN geo_point AS gp ON gp.id = gr.geo_point_id
        INNER JOIN product AS p ON p.base_product_id = grr.base_product_id
        WHERE grr.goods_condition_code = 'free' AND gp.geo_city_id = p.geo_city_id
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

WITH
    data (product_id, inner_transit) AS (
        SELECT p.id, TRUE
        FROM goods_reserve_register_current AS grr
        INNER JOIN goods_release_doc AS gre ON gre.number = grr.goods_release_id
        INNER JOIN geo_room AS gr ON gr.id = gre.destination_room_id
        INNER JOIN geo_point AS gp ON gp.id = gr.geo_point_id
        INNER JOIN product AS p ON p.base_product_id = grr.base_product_id
        WHERE grr.goods_condition_code = 'free' AND p.geo_city_id IS NULL
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
        FROM goods_reserve_register_current AS grr
        INNER JOIN supply_item AS si ON si.id = grr.supply_item_id
        INNER JOIN supply AS s ON s.ID = si.parent_doc_id AND si.parent_doc_type = 'supply'
        INNER JOIN geo_point AS gp ON gp.id = s.destination_point_id
        INNER JOIN product AS p ON p.base_product_id = grr.base_product_id
        WHERE grr.goods_condition_code = 'free' AND gp.geo_city_id = p.geo_city_id
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

WITH
    data (product_id, supplier_transit) AS (
        SELECT p.id, TRUE
        FROM goods_reserve_register_current AS grr
        INNER JOIN supply_item AS si ON si.id = grr.supply_item_id
        INNER JOIN supply_doc AS s ON s.ID = si.parent_doc_id AND si.parent_doc_type = 'supply'
        INNER JOIN geo_point AS gp ON gp.id = s.destination_point_id
        INNER JOIN product AS p ON p.base_product_id = grr.base_product_id
        WHERE grr.goods_condition_code = 'free' AND p.geo_city_id IS NULL
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

-- временные таблицы

DROP TABLE IF EXISTS product_tmp_available;
DROP TABLE IF EXISTS product_tmp_on_demand;
DROP TABLE IF EXISTS product_tmp_out_of_stock;

DROP TABLE IF EXISTS product_tmp;

-- для скорости таблицы не логируемые, индексы нужны для заполнения

CREATE UNLOGGED TABLE product_tmp AS SELECT * FROM product LIMIT 0;
ALTER TABLE product_tmp ADD CONSTRAINT product_tmp_pkey PRIMARY KEY (id);

CREATE UNLOGGED TABLE product_tmp_available () INHERITS (product_tmp);
ALTER TABLE product_tmp_available ADD CONSTRAINT product_tmp_available_pkey PRIMARY KEY (id);
CREATE INDEX product_tmp_available_base_product_id_idx ON product_tmp_available USING btree (
    base_product_id ASC NULLS LAST
);

CREATE UNLOGGED TABLE product_tmp_on_demand () INHERITS (product_tmp);
ALTER TABLE product_tmp_on_demand ADD CONSTRAINT product_tmp_on_demand_pkey PRIMARY KEY (id);
CREATE INDEX product_tmp_on_demand_base_product_id_idx ON product_tmp_on_demand USING btree (
    base_product_id ASC NULLS LAST
);

CREATE UNLOGGED TABLE product_tmp_out_of_stock () INHERITS (product_tmp);
ALTER TABLE product_tmp_out_of_stock ADD CONSTRAINT product_tmp_out_of_stock_pkey PRIMARY KEY (id);

-- товары в наличии, включают товары на перемещении и в транзите к нам от поставщика

INSERT INTO product_tmp_available (
    id,
    geo_city_id,
    base_product_id,
    product_availability_code,
    price,
    price_type,
    price_time,
    discount_amount,
    offer_percent,
    created_at,
    modified_at,
    delivery_tax,
    lifting_tax,
    manual_price,
    manual_price_operated_by,
    manual_price_operated_at,
    ultimate_price,
    ultimate_price_operated_by,
    ultimate_price_operated_at,
    competitor_price,
    temporary_price,
    temporary_price_operated_at,
    temporary_price_operated_by,
    rating
)
SELECT
    product.id,
    product.geo_city_id,
    product.base_product_id,
    (CASE WHEN product_tmp_reserve.free_reserve = TRUE THEN 'available' ELSE 'in_transit' END)::product_availability_code,
    product.price,
    product.price_type,
    product.price_time,
    product.discount_amount,
    product.offer_percent,
    product.created_at,
    product.modified_at,
    product.delivery_tax,
    product.lifting_tax,
    product.manual_price,
    product.manual_price_operated_by,
    product.manual_price_operated_at,
    product.ultimate_price,
    product.ultimate_price_operated_by,
    product.ultimate_price_operated_at,
    product.competitor_price,
    product.temporary_price,
    product.temporary_price_operated_at,
    product.temporary_price_operated_by,
    product.rating
FROM product
INNER JOIN product_tmp_reserve ON product_tmp_reserve.product_id = product.id;

-- товары в наличии для других городов (доступны под заказ)

INSERT INTO product_tmp_available (
    id,
    geo_city_id,
    base_product_id,
    product_availability_code,
    price,
    price_type,
    price_time,
    discount_amount,
    offer_percent,
    created_at,
    modified_at,
    delivery_tax,
    lifting_tax,
    manual_price,
    manual_price_operated_by,
    manual_price_operated_at,
    ultimate_price,
    ultimate_price_operated_by,
    ultimate_price_operated_at,
    competitor_price,
    temporary_price,
    temporary_price_operated_at,
    temporary_price_operated_by,
    rating
)
SELECT
    product.id,
    product.geo_city_id,
    product.base_product_id,
    'on_demand'::product_availability_code,
    product.price,
    product.price_type,
    product.price_time,
    product.discount_amount,
    product.offer_percent,
    product.created_at,
    product.modified_at,
    product.delivery_tax,
    product.lifting_tax,
    product.manual_price,
    product.manual_price_operated_by,
    product.manual_price_operated_at,
    product.ultimate_price,
    product.ultimate_price_operated_by,
    product.ultimate_price_operated_at,
    product.competitor_price,
    product.temporary_price,
    product.temporary_price_operated_at,
    product.temporary_price_operated_by,
    product.rating
FROM product
INNER JOIN product_tmp_available on product_tmp_available.base_product_id = product.base_product_id
ON CONFLICT DO NOTHING;

-- товары под заказ у поставщика, а также в тразите у постащика (ожидаются)

INSERT INTO product_tmp_on_demand (
    id,
    geo_city_id,
    base_product_id,
    product_availability_code,
    price,
    price_type,
    price_time,
    discount_amount,
    offer_percent,
    created_at,
    modified_at,
    delivery_tax,
    lifting_tax,
    manual_price,
    manual_price_operated_by,
    manual_price_operated_at,
    ultimate_price,
    ultimate_price_operated_by,
    ultimate_price_operated_at,
    competitor_price,
    temporary_price,
    temporary_price_operated_at,
    temporary_price_operated_by,
    rating
)
SELECT
    product.id,
    product.geo_city_id,
    product.base_product_id,
    (CASE WHEN base_product.supplier_availability_code = 'in_transit' THEN 'awaiting' ELSE 'on_demand' END)::product_availability_code,
    product.price,
    product.price_type,
    product.price_time,
    product.discount_amount,
    product.offer_percent,
    product.created_at,
    product.modified_at,
    product.delivery_tax,
    product.lifting_tax,
    product.manual_price,
    product.manual_price_operated_by,
    product.manual_price_operated_at,
    product.ultimate_price,
    product.ultimate_price_operated_by,
    product.ultimate_price_operated_at,
    product.competitor_price,
    product.temporary_price,
    product.temporary_price_operated_at,
    product.temporary_price_operated_by,
    product.rating
FROM product
INNER JOIN base_product ON base_product.id = product.base_product_id
LEFT OUTER JOIN product_tmp ON product_tmp.id = product.id
WHERE base_product.supplier_availability_code IN ('available', 'on_demand', 'in_transit') AND product_tmp.id IS NULL;

-- нет в наличии

INSERT INTO product_tmp_out_of_stock (
    id,
    geo_city_id,
    base_product_id,
    product_availability_code,
    price,
    price_type,
    price_time,
    discount_amount,
    offer_percent,
    created_at,
    modified_at,
    delivery_tax,
    lifting_tax,
    manual_price,
    manual_price_operated_by,
    manual_price_operated_at,
    ultimate_price,
    ultimate_price_operated_by,
    ultimate_price_operated_at,
    competitor_price,
    temporary_price,
    temporary_price_operated_at,
    temporary_price_operated_by,
    rating
)
SELECT
    product.id,
    product.geo_city_id,
    product.base_product_id,
    'out_of_stock'::product_availability_code,
    product.price,
    product.price_type,
    product.price_time,
    product.discount_amount,
    product.offer_percent,
    product.created_at,
    product.modified_at,
    product.delivery_tax,
    product.lifting_tax,
    product.manual_price,
    product.manual_price_operated_by,
    product.manual_price_operated_at,
    product.ultimate_price,
    product.ultimate_price_operated_by,
    product.ultimate_price_operated_at,
    product.competitor_price,
    product.temporary_price,
    product.temporary_price_operated_at,
    product.temporary_price_operated_by,
    product.rating
FROM product
LEFT OUTER JOIN product_tmp ON product_tmp.id = product.id
WHERE product_tmp.id IS NULL;

-- расчет цен товаров в наличии

UPDATE product_tmp_available
SET
    price = data.price,
    price_type = data.price_type::product_price_type,
    profit = data.purchase_price - data.price
FROM (
    WITH
        category_delivery AS (
            SELECT
                p.id product_id,
                COALESCE(
                    (SELECT
                        c.delivery_tax
                    FROM base_product bp
                    INNER JOIN category_path cp ON cp.id = bp.category_id
                    INNER JOIN category c ON c.id = cp.pid
                    WHERE c.delivery_tax > 0 AND bp.id = p.base_product_id
                    ORDER BY cp.plevel DESC
                    LIMIT 1), 0
                ) AS delivery_tax
            FROM product_tmp_available p
        ),
        competitor_product AS (
            SELECT
                p.id AS product_id,
                MIN(p2c.competitor_price) AS competitor_price
            FROM product_tmp_available AS p
            INNER JOIN product_to_competitor AS p2c ON p2c.base_product_id = p.base_product_id
            INNER JOIN competitor AS c ON c.id = p2c.competitor_id
            WHERE c.is_active = true AND p2c.competitor_price > 0
                AND
                CASE
                    WHEN c.channel IN ('site', 'retail')
                    THEN p2c.price_time + INTERVAL '7 day' >= NOW()
                    ELSE p2c.price_time + INTERVAL '30 day' >= NOW()
                END
                AND (
                    COALESCE(p2c.geo_city_id, p.geo_city_id) = p.geo_city_id
                    OR (p.geo_city_id IS NULL AND p2c.geo_city_id IS NULL)
                )
            GROUP BY product_id
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
            FROM product_tmp_available p
            INNER JOIN base_product bp ON bp.id = p.base_product_id
        )
    SELECT
        p.id AS product_id,
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
        END AS price_type
    FROM product_tmp_available p
    INNER JOIN base_product AS bp ON bp.id = p.base_product_id
    LEFT JOIN category_delivery AS cd ON cd.product_id = p.id
    LEFT JOIN competitor_product AS cp ON cp.product_id = p.id
    LEFT JOIN standard_product AS sp ON sp.product_id = p.id
) AS data
WHERE product_tmp_available.id = data.product_id;

-- расчет цен товаров под заказ (расчет производится для нулевого города)

UPDATE product_tmp_on_demand
SET
    price = data.price,
    price_type = data.price_type::product_price_type,
    profit = data.purchase_price - data.price
FROM (
    WITH
        category_delivery AS (
            SELECT
                p.id product_id,
                COALESCE(
                    (SELECT
                        c.delivery_tax
                    FROM base_product bp
                    INNER JOIN category_path cp ON cp.id = bp.category_id
                    INNER JOIN category c ON c.id = cp.pid
                    WHERE c.delivery_tax > 0 AND bp.id = p.base_product_id
                    ORDER BY cp.plevel DESC
                    LIMIT 1), 0
                ) AS delivery_tax
            FROM product_tmp_on_demand p
            WHERE p.geo_city_id IS NULL
        ),
        competitor_product AS (
            SELECT
                p.id AS product_id,
                MIN(p2c.competitor_price) AS competitor_price
            FROM product_tmp_on_demand AS p
            INNER JOIN product_to_competitor AS p2c ON p2c.base_product_id = p.base_product_id
            INNER JOIN competitor AS c ON c.id = p2c.competitor_id
            WHERE c.is_active = true AND p2c.competitor_price > 0
                AND
                CASE
                    WHEN c.channel IN ('site', 'retail')
                    THEN p2c.price_time + INTERVAL '7 day' >= NOW()
                    ELSE p2c.price_time + INTERVAL '30 day' >= NOW()
                END
                AND p.geo_city_id IS NULL
                AND p2c.geo_city_id IS NULL
            GROUP BY product_id
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
            FROM product_tmp_on_demand p
            INNER JOIN base_product bp ON bp.id = p.base_product_id
            WHERE p.geo_city_id IS NULL
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
            WHEN p.manual_price > 0 THEN 'manual'
            WHEN cp.competitor_price > 0 AND sp.retail_price + cd.delivery_tax > cp.competitor_price
                AND cp.competitor_price > bp.supplier_price THEN 'compared'
            WHEN bp.price_retail_min > sp.retail_price THEN 'recommended'
            ELSE 'standard'
        END AS price_type
    FROM product_tmp_on_demand p
    INNER JOIN base_product AS bp ON bp.id = p.base_product_id
    LEFT JOIN category_delivery AS cd ON cd.product_id = p.id
    LEFT JOIN competitor_product AS cp ON cp.product_id = p.id
    LEFT JOIN standard_product AS sp ON sp.product_id = p.id
    WHERE p.geo_city_id IS NULL
) AS data
WHERE product_tmp_on_demand.id = data.product_id;

-- быстрое копирование цен с нулевого города в остальные

UPDATE product_tmp_on_demand
SET
    price = p.price,
    price_type = p.price_type,
    profit = p.profit
FROM (
    SELECT base_product_id, price, price_type, profit
    FROM product_tmp_on_demand
    WHERE geo_city_id IS NULL
) p
WHERE product_tmp_on_demand.base_product_id = p.base_product_id AND product_tmp_on_demand.geo_city_id IS NOT NULL;

-- финальная таблица товаров
-- установка логирования и создание всех необходимых индексов и ограничений

DROP TABLE IF EXISTS product_tmp_final;
CREATE UNLOGGED TABLE product_tmp_final AS SELECT * FROM product_tmp_out_of_stock;
INSERT INTO product_tmp_final SELECT * FROM product_tmp_on_demand;
INSERT INTO product_tmp_final SELECT * FROM product_tmp_available;

ALTER TABLE product_tmp_final ADD CONSTRAINT product_tmp_final_pkey PRIMARY KEY (id);

CREATE UNIQUE INDEX product_tmp_final_base_product_id_idx ON product_tmp_final USING btree (
    base_product_id ASC NULLS LAST
) WHERE geo_city_id IS NULL;
CREATE UNIQUE INDEX product_tmp_final_geo_city_id_base_product_id_idx ON product_tmp_final USING btree (
  geo_city_id ASC NULLS LAST,
  base_product_id ASC NULLS LAST
) WHERE geo_city_id IS NOT NULL;
CREATE INDEX product_tmp_final_product_availability_code_idx ON product_tmp_final USING btree (
  product_availability_code ASC NULLS LAST
);

ALTER TABLE product_tmp_final ADD CONSTRAINT product_tmp_final_base_product_id_fkey
    FOREIGN KEY (base_product_id) REFERENCES base_product(id) ON DELETE CASCADE ON UPDATE CASCADE;
ALTER TABLE product_tmp_final ADD CONSTRAINT product_tmp_final_geo_city_id_fkey
    FOREIGN KEY (geo_city_id) REFERENCES geo_city(id) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE product_tmp_final SET LOGGED;

-- подмена таблиц

DROP TABLE IF EXISTS product;
ALTER TABLE product_tmp_final RENAME TO product;

-- переименовывание индесов и ограничений

ALTER INDEX product_tmp_final_pkey RENAME TO product_pkey;
ALTER INDEX product_tmp_final_base_product_id_idx RENAME TO product_base_product_id_idx;
ALTER INDEX product_tmp_final_geo_city_id_base_product_id_idx RENAME TO product_geo_city_id_base_product_id_idx;
ALTER INDEX product_tmp_final_product_availability_code_idx RENAME TO product_product_availability_code_idx;

ALTER TABLE product RENAME CONSTRAINT product_tmp_final_base_product_id_fkey TO product_base_product_id_fkey;
ALTER TABLE product RENAME CONSTRAINT product_tmp_final_geo_city_id_fkey TO product_geo_city_id_fkey;

-- удаление временных таблиц

DROP TABLE IF EXISTS product_tmp_reserve;
DROP TABLE IF EXISTS product_tmp_available;
DROP TABLE IF EXISTS product_tmp_on_demand;
DROP TABLE IF EXISTS product_tmp_out_of_stock;
DROP TABLE IF EXISTS product_tmp;
