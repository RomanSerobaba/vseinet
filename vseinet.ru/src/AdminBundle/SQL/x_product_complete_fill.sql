CREATE OR REPLACE FUNCTION x_product_complete_fill()
  RETURNS void AS $BODY$
BEGIN
  PERFORM switch_all_triggers(FALSE);

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
    COALESCE(p.product_availability_code, 'out_of_stock'),
    COALESCE(p.price, 0),
    COALESCE(p.price_type, 'standard'),
    COALESCE(p.price_time, NOW()),
    COALESCE(p.discount_amount, 0),
    COALESCE(p.created_at, NOW()),
    COALESCE(p.rating, 0),
    COALESCE(p.profit, 0)
  FROM aggregation.product_0 AS p0
  RIGHT OUTER JOIN public.base_product AS bp ON bp.id = p0.base_product_id
  LEFT OUTER JOIN public.product AS p ON p.base_product_id = bp.id AND p.geo_city_id = 1
  WHERE p0.id IS NULL;

  PERFORM switch_all_triggers(TRUE);
END
$BODY$
  LANGUAGE 'plpgsql' VOLATILE;
