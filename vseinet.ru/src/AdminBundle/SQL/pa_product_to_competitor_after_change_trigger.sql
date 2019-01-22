CREATE OR REPLACE FUNCTION pa_product_to_competitor_after_change_trigger()
  RETURNS trigger AS $BODY$
DECLARE
  row record;
BEGIN
  IF TG_OP = 'DELETE' THEN
    row = OLD;
  ELSE
    row = NEW;
  END IF;

  INSERT INTO product_update_register (id, geo_city_id)
  VALUES ('
    SELECT p.id, p.geo_city_id
    FROM product AS p
    WHERE p.geo_city_id = ' || row.geo_city_id || ' AND p.base_product_id = ' || row.base_product_id
  );

  RETURN row;
END
$BODY$
   LANGUAGE 'plpgsql' VOLATILE;

DROP TRIGGER IF EXISTS pa_product_to_competitor_after_change_trigger ON product_to_competitor;

CREATE TRIGGER pa_product_to_competitor_after_change_trigger
AFTER INSERT OR UPDATE OF competitor_price OR DELETE ON product_to_competitor
FOR EACH ROW
EXECUTE PROCEDURE pa_product_to_competitor_after_change_trigger();