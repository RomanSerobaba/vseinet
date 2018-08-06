SELECT 
    bp.id,
    bp.name, 
    bp.category_id,
    COALESCE(bp.category_section_id, 0) AS section_id,
    bp.brand_id,
    b.is_forbidden,
    bpd.details,
    bpi.availability,
    bpi.price,
    bpi.price_order,
    bpi.profit,
    bpi.rating,
    bpi.nofilled,
    bp.created_at::timestamp AS created_at,
    0 AS killbill
FROM base_product AS bp 
INNER JOIN base_product_data AS bpd ON bpd.base_product_id = bp.id
INNER JOIN base_product_index as bpi ON bpi.base_product_id = bp.id 
INNER JOIN brand AS b ON b.id = bp.brand_id
WHERE bp.id BETWEEN $start AND $end 
