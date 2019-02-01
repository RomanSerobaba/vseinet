CREATE OR REPLACE FUNCTION product_before_insert_trigger()
  RETURNS trigger AS $BODY$
BEGIN
  EXECUTE 'INSERT INTO aggregation.product_' || NEW.geo_city_id || '  VALUES ($1.*)'
  USING NEW;

  RETURN NULL;
END
$BODY$
  LANGUAGE 'plpgsql' VOLATILE;

-- #
DROP TRIGGER IF EXISTS product_before_insert_trigger ON product;

-- #
CREATE TRIGGER product_before_insert_trigger
BEFORE INSERT ON product
FOR EACH ROW
EXECUTE PROCEDURE product_before_insert_trigger();
