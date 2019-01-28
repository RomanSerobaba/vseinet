CREATE OR REPLACE FUNCTION pa_update_all()
  RETURNS void AS $BODY$
DECLARE
  cities CURSOR FOR
    WITH geo AS (
      SELECT gp.geo_city_id, gc.is_central
      FROM geo_point AS gp
      INNER JOIN representative AS r ON r.geo_point_id = gp.id
      INNER JOIN geo_city AS gc ON gc.id = gp.geo_city_id
      WHERE r.is_active = TRUE AND r.has_retail = TRUE AND r.type IN ('our', 'torg', 'partner')
      GROUP BY gp.geo_city_id, gc.is_central
      UNION
      SELECT 0, FALSE AS geo_city_id
    )
    SELECT geo_city_id
    FROM geo
    ORDER BY CASE WHEN is_central THEN 1 ELSE 2 END;
    row record;
BEGIN
  FOR row IN cities LOOP
    PERFORM pg_update(row.geo_city_id, NULL, TRUE);
    -- PERFORM pg_notify('product_index_all', row.geo_city_id);
  END LOOP;
END
$BODY$
  LANGUAGE 'plpgsql' VOLATILE;
