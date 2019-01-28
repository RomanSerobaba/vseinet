CREATE OR REPLACE FUNCTION x_supplier_pricelist_load_all()
  RETURNS void AS $BODY$
DECLARE
  suppliers CURSOR FOR
    SELECT supplier_id AS id
    FROM base_product
    GROUP BY supplier_id;
  supplier record;
BEGIN
  FOR supplier IN suppliers LOOP
    PERFORM supplier_pricelist_after_load(supplier.id);
  END LOOP;
END
$BODY$
  LANGUAGE 'plpgsql' VOLATILE;