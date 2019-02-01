CREATE OR REPLACE FUNCTION pa_goods_reserve_register_current_after_change_trigger()
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
DROP TRIGGER IF EXISTS pa_goods_reserve_register_current_after_change_trigger ON goods_reserve_register_current;

-- #
CREATE TRIGGER pa_goods_reserve_register_current_after_change_trigger
AFTER INSERT OR UPDATE OR DELETE ON goods_reserve_register_current
FOR EACH ROW
EXECUTE PROCEDURE pa_goods_reserve_register_current_after_change_trigger();