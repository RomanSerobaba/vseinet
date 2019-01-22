CREATE OR REPLACE FUNCTION pa_supplier_product_after_update_trigger()
  RETURNS trigger AS $BODY$
BEGIN
  INSERT INTO product_update_register (id, geo_city_id)
  VALUES ('
    SELECT p.id, p.geo_city_id
    FROM product AS p
    WHERE p.base_product_id IN (' || NEW.base_product_id || ',' || OLD.base_product_id || ')'
  );

  RETURN NEW;
END
$BODY$
  LANGUAGE 'plpgsql' VOLATILE;

DROP TRIGGER IF EXISTS pa_supplier_product_after_update_trigger ON supplier_product;

CREATE TRIGGER pa_supplier_product_after_update_trigger
AFTER UPDATE OF base_product_id, product_availability_code, price, price_retail_min, competitor_price ON supplier_product
FOR EACH ROW
EXECUTE PROCEDURE pa_supplier_product_after_update_trigger();