CREATE OR REPLACE FUNCTION product_complete_fill()
  RETURNS void AS $BODY$
DECLARE
  cities CURSOR FOR
    SELECT gp.geo_city_id
    FROM geo_point AS gp
    INNER JOIN representative AS r ON r.geo_point_id = gp.id
    WHERE r.is_active = true AND r.has_retail = true AND r.type IN ('our', 'torg', 'partner')
    GROUP BY gp.geo_city_id;
  row record;
BEGIN
  PERFORM switch_all_triggers(false);

  -- дозаполнение нулевого города
  INSERT INTO aggregation.product_0 (
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
    rating,
    profit
  )
  SELECT
    nextval('product_id_seq'::regclass),
    0,
    bp.id,
    COALESCE(p1.product_availability_code, 'out_of_stock'),
    COALESCE(p1.price, 0),
    COALESCE(p1.price_type, 'standard'),
    COALESCE(p1.price_time, NOW()),
    COALESCE(p1.discount_amount, 0),
    COALESCE(p1.offer_percent, 0),
    COALESCE(p1.created_at, NOW()),
    COALESCE(p1.modified_at, NOW()),
    COALESCE(p1.delivery_tax, 0),
    COALESCE(p1.lifting_tax, 0),
    COALESCE(p1.rating, 0),
    COALESCE(p1.profit, 0)
  FROM aggregation.product_0 AS p0
  RIGHT OUTER JOIN public.base_product AS bp ON bp.id = p0.base_product_id
  LEFT OUTER JOIN public.product AS p1 ON p1.base_product_id = bp.id AND p1.geo_city_id = 1
  WHERE p0.id IS NULL;

  -- дозаполнение остальных городов
  FOR row IN cities LOOP
    EXECUTE '
      INSERT INTO aggregation.product_' || row.geo_city_id || ' (
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
        rating,
        profit
      )
      SELECT
        nextval(''product_id_seq''::regclass),
        ' || row.geo_city_id || ',
        p0.base_product_id,
        p0.product_availability_code,
        p0.price,
        p0.price_type,
        p0.price_time,
        p0.discount_amount,
        p0.offer_percent,
        p0.created_at,
        p0.modified_at,
        p0.delivery_tax,
        p0.lifting_tax,
        p0.rating,
        p0.profit
      FROM aggregation.product_0 AS p0
      LEFT OUTER JOIN aggregation.product_' || row.geo_city_id || ' AS p ON p.base_product_id = p0.base_product_id
      WHERE p.id IS NULL
    ';
  END LOOP;

  PERFORM switch_all_triggers(true);
END
$BODY$
  LANGUAGE 'plpgsql' VOLATILE;
