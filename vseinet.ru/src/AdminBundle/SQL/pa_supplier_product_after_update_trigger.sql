CREATE OR REPLACE FUNCTION pa_supplier_product_after_update_trigger()
  RETURNS trigger AS $BODY$
DECLARE
  row record;
BEGIN
  IF TG_OP = 'DELETE' THEN
    row = OLD;
  ELSE
    row = NEW;
  END IF;

  INSERT INTO product_update_register (base_product_id) VALUES (row.base_product_id);

  RETURN row;
END
$BODY$
  LANGUAGE 'plpgsql' VOLATILE;

-- #
DROP TRIGGER IF EXISTS pa_supplier_product_after_update_trigger ON supplier_product;

-- #
CREATE TRIGGER pa_supplier_product_after_update_trigger
AFTER UPDATE OF base_product_id, product_availability_code, price, price_retail_min, competitor_price ON supplier_product
FOR EACH ROW
EXECUTE PROCEDURE pa_supplier_product_after_update_trigger();