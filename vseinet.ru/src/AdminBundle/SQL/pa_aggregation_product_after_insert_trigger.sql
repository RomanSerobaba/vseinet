CREATE OR REPLACE FUNCTION aggregation.product_after_insert_trigger()
  RETURNS trigger AS $BODY$
BEGIN
  PERFORM public.pa_update(NEW.base_product_id, FALSE);
  -- PERFORM pg_notify('product_index_immediate', NEW.id);

  RETURN NEW;
END
$BODY$
  LANGUAGE 'plpgsql' VOLATILE;