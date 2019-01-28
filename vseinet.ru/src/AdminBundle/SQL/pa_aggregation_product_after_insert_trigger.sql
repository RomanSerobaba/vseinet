CREATE OR REPLACE FUNCTION aggregation.product_after_insert_trigger()
  RETURNS trigger AS $BODY$
DECLARE
  partition_update_function_name text = 'product_update_' || NEW.geo_city_id;
BEGIN
  PERFORM aggregation.pa_update(NEW.geo_city_id, NEW.id, FALSE);
  -- PERFORM pg_notify('product_index_immediate', NEW.id);

  RETURN NEW;
END
$BODY$
  LANGUAGE 'plpgsql' VOLATILE;