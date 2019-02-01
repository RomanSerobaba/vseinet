CREATE OR REPLACE FUNCTION pa_category_after_update_trigger()
  RETURNS trigger AS $BODY$
BEGIN
  INSERT INTO product_update_register (base_product_id)
  SELECT bp.id
  FROM base_product AS bp
  INNER JOIN category_path AS cp ON cp.id = bp.category_id
  WHERE cp.pid = NEW.id;

  RETURN NEW;
END
$BODY$
    LANGUAGE 'plpgsql' VOLATILE;

-- #
DROP TRIGGER IF EXISTS pa_category_after_update_trigger ON category;

-- #
CREATE TRIGGER pa_category_after_update_trigger
AFTER UPDATE OF delivery_tax, lifting_tax ON category
FOR EACH ROW
EXECUTE PROCEDURE pa_category_after_update_trigger();