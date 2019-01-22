CREATE OR REPLACE FUNCTION base_product_after_insert_trigger()
  RETURNS trigger AS $BODY$
DECLARE
  cities CURSOR FOR
    SELECT gp.geo_city_id
    FROM geo_point AS gp
    INNER JOIN representative AS r ON r.geo_point_id = gp.id
    WHERE r.is_active = true AND r.has_retail = true AND r.type IN ('our', 'torg', 'partner')
    GROUP BY gp.geo_city_id
    UNION
    SELECT 0 AS geo_city_id;
  row record;
BEGIN
  FOR row IN cities LOOP
    EXECUTE '
      INSERT INTO product_' || row.geo_city_id::text || ' (
        geo_city_id,
        base_product_id,
        product_availability_code,
        price,
        price_type,
        price_time,
        created_at,
        rating) ' || '
      VALUES ( $1, $2, $3, 0, ''pricelist''::product_availability_code, $4, $4, 0)'
    USING
      row.geo_city_id,
      NEW.id,
      NEW.supplier_availability_code::product_availability_code,
      NEW.created_at;
  END LOOP;

  RETURN NEW;
END
$BODY$
  LANGUAGE 'plpgsql' VOLATILE;

DROP TRIGGER IF EXISTS base_product_after_insert_trigger ON base_product;

CREATE TRIGGER base_product_after_insert_trigger
AFTER INSERT ON base_product
FOR EACH ROW
EXECUTE PROCEDURE base_product_after_insert_trigger();