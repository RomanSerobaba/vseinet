DROP TABLE IF EXISTS base_product_index;

CREATE UNLOGGED TABLE base_product_index (
  base_product_id INTEGER NOT NULL,
  availability json NOT NULL,
  price json NOT NULL,
  price_order json NOT NULL,
  profit json NOT NULL,
  rating json NOT NULL,
  nofilled json DEFAULT NULL
);

-- AppBundle\Bus\Catalog\Query\Enum\Availability.php

INSERT INTO base_product_index (
    base_product_id,
    availability,
    price,
    price_order,
    profit,
    rating
)
SELECT 
    p.base_product_id,
    json_object_agg(p.geo_city_id,
        CASE 
            WHEN p.product_availability_code = 'available' THEN 1 -- available
            WHEN p.product_availability_code IN ('in_transit', 'on_demand') THEN 2 -- on_demand (+available)
            WHEN p.product_availability_code = 'awaiting' THEN 3 -- active (+on_demand)
            ELSE 4 -- for_all_time (+active)
        END
    ) AS availability,
    json_object_agg(p.geo_city_id, ROUND(p.price / 100)) AS price, -- in rubles
    json_object_agg(p.geo_city_id, 
        CASE 
            WHEN p.product_availability_code = 'available' THEN 1
            WHEN p.product_availability_code = 'in_transit' THEN 2
            WHEN p.product_availability_code = 'on_demand' THEN 3
            ELSE 4
        END
    ) AS price_order,
    json_object_agg(p.geo_city_id, p.profit) AS profit, -- in pennies
    json_object_agg(p.geo_city_id, p.rating) AS rating
FROM product AS p
GROUP BY p.base_product_id;

ALTER TABLE base_product_index ADD CONSTRAINT base_product_index_pkey PRIMARY KEY (base_product_id);

-- AppBundle\Bus\Catalog\Query\Enum\Nofilled.php

UPDATE base_product_index
SET 
    nofilled = data.nofilled 
FROM (
    SELECT
        bp.id, 
        json_build_object(
            -- details 
            1, CASE WHEN bpd.details IS NULL THEN 1 ELSE 0 END,
            -- images 
            2, CASE WHEN bpi.id IS NULL THEN 1 ELSE 0 END,
            -- description
            3, CASE WHEN d.base_product_id IS NULL THEN 1 ELSE 0 END,
            -- manufacturer_link
            4, CASE WHEN COALESCE(bpd.manufacturer_link, '') = '' THEN 1 ELSE 0 END,
            -- manual_link
            5, CASE WHEN COALESCE(bpd.manual_link, '') = '' THEN 1 ELSE 0 END
        ) AS nofilled    
    FROM base_product AS bp
    INNER JOIN base_product_data AS bpd ON bpd.base_product_id = bp.id 
    LEFT OUTER JOIN base_product_image AS bpi ON bpi.base_product_id = bp.id AND bpi.sort_order = 1
    LEFT OUTER JOIN base_product_description AS d ON d.base_product_id = bp.id
) AS data 
WHERE base_product_index.base_product_id = data.id;

-- характеристики

UPDATE base_product_data
SET 
    details = data.details 
FROM (
    SELECT 
        cd2p.base_product_id AS id,
        json_object_agg(cd2p.content_detail_id, COALESCE(cd2p.content_detail_value_id, cd2p.value)) AS details
    FROM content_detail_to_product AS cd2p 
    GROUP BY cd2p.base_product_id
) AS data 
WHERE base_product_data.base_product_id = data.id;