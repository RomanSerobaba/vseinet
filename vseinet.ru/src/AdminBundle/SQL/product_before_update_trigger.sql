CREATE OR REPLACE FUNCTION product_before_update_trigger()
  RETURNS trigger AS $BODY$
DECLARE
  partition_name text = 'product_' || NEW.geo_city_id;
BEGIN
  EXECUTE 'DELETE FROM aggregation.' || partition_name || ' WHERE id = $1'
  USING NEW.id;

  EXECUTE 'INSERT INTO aggregation.' || partition_name || ' VALUES ($1.*)'
  USING NEW;

  RETURN NULL;
END
$BODY$
  LANGUAGE 'plpgsql' VOLATILE;

-- #
DROP TRIGGER IF EXISTS product_before_update_trigger ON product;

-- #
CREATE TRIGGER product_before_update_trigger
BEFORE UPDATE ON product
FOR EACH ROW
EXECUTE PROCEDURE product_before_update_trigger();
