CREATE OR REPLACE FUNCTION pa_base_product_after_update_trigger()
  RETURNS trigger AS $BODY$
BEGIN
  INSERT INTO product_update_register (base_product_id) VALUES (NEW.id);

  RETURN NEW;
END
$BODY$
   LANGUAGE 'plpgsql' VOLATILE;

-- #
DROP TRIGGER IF EXISTS pa_base_product_after_update_trigger ON base_product;

-- #
CREATE TRIGGER pa_base_product_after_update_trigger
AFTER UPDATE OF category_id ON base_product
FOR EACH ROW
EXECUTE PROCEDURE pa_base_product_after_update_trigger();

