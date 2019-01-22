CREATE OR REPLACE FUNCTION pa_trade_margin_after_change_trigger()
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
    INNER JOIN base_product AS bp ON bp.id = b.base_product_id
    INNER JOIN category_path AS cp ON cp.id = bp.category_id
    WHERE cp.pid = ' || row.category_id
  );

  RETURN row;
END
$BODY$
  LANGUAGE 'plpgsql' VOLATILE;

DROP TRIGGER IF EXISTS pa_trade_margin_after_upsert_trigger ON trade_margin;

CREATE TRIGGER pa_trade_margin_after_upsert_trigger
AFTER INSERT OR UPDATE OF margin_percent, lower_limit, higher_limit OR DELETE ON trade_margin
FOR EACH ROW
EXECUTE PROCEDURE pa_trade_margin_after_upsert_trigger();