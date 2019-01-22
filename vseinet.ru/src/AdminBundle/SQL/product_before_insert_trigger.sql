CREATE OR REPLACE FUNCTION public.product_before_insert_trigger()
  RETURNS trigger AS $BODY$
DECLARE
  partition_name text = 'product_' || NEW.geo_city_id;
BEGIN
  EXECUTE 'INSERT INTO public.product_pk (id) VALUES($1)'
  USING NEW.id;

  EXECUTE 'INSERT INTO aggregation.' || partition_name || '  VALUES ($1.*)'
  USING NEW;

  RETURN NULL;
END
$BODY$
  LANGUAGE 'plpgsql' VOLATILE;

DROP TRIGGER IF EXISTS product_before_insert_trigger ON product_before_insert_trigger;

CREATE TRIGGER product_before_insert_trigger
BEFORE INSERT ON product
FOR EACH ROW
EXECUTE PROCEDURE product_before_insert_trigger();
