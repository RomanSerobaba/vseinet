CREATE OR REPLACE FUNCTION base_product_after_insert_trigger()
  RETURNS trigger AS $BODY$
BEGIN
  INSERT INTO aggregation.product_0 (
    geo_city_id,
    base_product_id,
    product_availability_code,
    price,
    price_type,
    price_time,
    created_at,
    rating,
    profit
  )
  VALUES (
    0,
    NEW.id,
    NEW.supplier_availability_code::product_availability_code,
    0,
    'standard'::product_price_type,
    NEW.created_at,
    NEW.created_at,
    0,
    0
  );

  RETURN NEW;
END
$BODY$
  LANGUAGE 'plpgsql' VOLATILE;

-- #
DROP TRIGGER IF EXISTS base_product_after_insert_trigger ON base_product;

-- #
CREATE TRIGGER base_product_after_insert_trigger
AFTER INSERT ON base_product
FOR EACH ROW
EXECUTE PROCEDURE base_product_after_insert_trigger();