CREATE OR REPLACE PROCEDURE supplier_pricelist_after_load(supplier_id int)
  RETURNS void AS $BODY$
BEGIN
  WITH
    data (id, supplier_id, supplier_availability_code, siplier_price, price_retail_min) AS (
      SELECT bp.id, sp.supplier_id, sp.product_availability_code, sp.price, sp.price_retail_min
      FROM supplier_product AS sp
      INNER JOIN base_product As bp ON bp.id = sp.base_product_id
      WHERE sp.supplier_id = supplier_id AND bp.supplier_id != supplier_id
        AND sp.product_availability_code > bp.supplier_availability_code
    )
  UPDATE base_product
  SET
    supplier_availability_code = data.supplier_availability_code,
    supplier_id = data.supplier_id,
    supplier_price = data.supplier_price,
    price_retail_min = data.price_retail_min
  FROM data
  WHERE base_product.id = data.id;

  WITH
    data (id, supplier_id, siplier_price, price_retail_min) AS (
      SELECT bp.id, sp.supplier_id, sp.product_availability_code, sp.price, sp.price_retail_min
      FROM supplier_product AS sp
      INNER JOIN base_product As bp ON bp.id = sp.base_product_id
      WHERE sp.supplier_id = supplier_id AND bp.supplier_id != supplier_id
        AND sp.product_availability_code = bp.supplier_availability_code AND sp.price < bp.supplier_price
    )
  UPDATE base_product
  SET
    supplier_id = data.supplier_id,
    supplier_price = data.supplier_price,
    price_retail_min = data.price_retail_min
  FROM data
  WHERE base_product.id = data.id;

  CREATE TEMP TABLE supplier_product_tmp AS
  SELECT sp.id, sp.supplier_id, sp.base_product_id, sp.product_availability_code, sp.price, sp.price_retail_min
  FROM supplier_product AS sp
  INNER JOIN base_product AS bp ON bp.id = sp.base_product_id
  LEFT OUTER JOIN supplier_product AS sp2 ON sp2.base_product_id = sp.base_product_id AND sp2.product_availability_code > sp.product_availability_code
  WHERE sp.price > 0 AND sp.supplier_id = supplier_id AND bp.supplier_id = supplier_id AND sp2.id IS NULL;

  ALTER TABLE supplier_product_tmp ADD CONSTRAINT supplier_product_tmp_pkey PRIMARY KEY(id);
  CREATE INDEX supplier_product_tmp_base_product_id ON supplier_product_tmp USING BTREE (base_product_id);

  WITH sp AS (
    SELECT sp.*
    FROM supplier_product_tmp AS sp
    LEFT OUTER JOIN supplier_product_tmp AS sp2 ON sp2.base_product_id = sp.base_product_id AND sp2.price < sp.price
    WHERE sp2.id IS NULL
  )
  UPDATE base_product AS bp
  INNER JOIN sp ON sp.base_product_id = bp.id
  SET
    supplier_availability_code = sp.supplier_availability_code,
    supplier_id = sp.supplier_id,
    supplier_price = sp.supplier_price,
    price_retail_min = sp.price_retail_min;

END
$BODY$
  LANGUAGE 'plpgsql' VOLATILE;