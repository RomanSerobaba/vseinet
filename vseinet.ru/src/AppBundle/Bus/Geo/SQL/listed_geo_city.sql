UPDATE geo_city 
SET is_listed = false;

UPDATE geo_city 
SET is_listed = true 
WHERE unit IN ('г', 'рп') AND name !~ '\d+';

UPDATE geo_city 
SET is_listed = true 
WHERE id IN (
    SELECT gc.id
    FROM geo_city AS gc
    INNER JOIN geo_point AS gp ON gp.geo_city_id = gc.id
    INNER JOIN representative AS r ON r.geo_point_id = gp.id
);

UPDATE geo_city 
SET is_listed = false 
WHERE char_length(name) > 19 AND is_listed = true;