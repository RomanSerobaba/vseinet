INSERT INTO geo_area (geo_region_id, name, "AOGUID", unit)
SELECT gr.id, gt."OFFNAME", gt."AOGUID"::uuid, gt."SHORTNAME"
FROM geo_temp AS gt 
INNER JOIN geo_region AS gr ON gr."AOGUID" = gt."PARENTGUID"
WHERE gt."AOLEVEL" = 3;

UPDATE geo_city 
SET geo_area_id = s.geo_area_id
FROM (
    SELECT c.id, a.id AS geo_area_id
    FROM geo_city AS c
    INNER JOIN geo_temp AS t ON t."AOGUID" = c."AOGUID"
    INNER JOIN geo_area AS a ON a."AOGUID"::text = t."PARENTGUID" 
) AS s
WHERE s.id = geo_city.id;