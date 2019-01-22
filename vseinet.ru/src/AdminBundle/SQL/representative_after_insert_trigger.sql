CREATE OR REPLACE FUNCTION public.representative_after_insert_trigger()
  RETURNS trigger AS $BODY$
DECLARE
  city_found record;
BEGIN
  SELECT geo_city_id INTO city_found FROM geo_point WHERE id = NEW.geo_point_id;
  -- создание сегмента
  SELECT product_create_partition(city_found.geo_city_id);
  -- создание функций обновления
  SELECT product_create_update_function(city_found.geo_city_id);
  SELECT product_create_update_function(city_found.geo_city_id, true);
END
$BODY$
  LANGUAGE 'plpgsql' VOLATILE;

DROP TRIGGER IF EXISTS representative_after_insert_trigger ON representative_after_insert_trigger;

CREATE TRIGGER representative_after_insert_trigger
AFTER INSERT ON representative
FOR EACH ROW
EXECUTE PROCEDURE representative_after_insert_trigger();