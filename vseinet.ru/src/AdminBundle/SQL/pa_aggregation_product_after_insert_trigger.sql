CREATE OR REPLACE FUNCTION aggregation.product_after_insert_trigger()
  RETURNS trigger AS $BODY$
DECLARE
  partition_update_function_name text = 'product_update_' || NEW.geo_city_id;
BEGIN
  EXECUTE 'SELECT aggregation.' || partition_update_function_name || '($1)'
  USING NEW.id;

  PERFORM pg_notify('product_index_immediate', NEW.id);

  RETURN NEW;
END
$BODY$
  LANGUAGE 'plpgsql' VOLATILE;