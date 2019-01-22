CREATE OR REPLACE FUNCTION public.product_create_partition(geo_city_id int)
  RETURNS void AS $BODY$
DECLARE
  partition_name text = 'product_' || geo_city_id;
  partition_update_register_name text = 'product_update_register_' || geo_city_id;
  partition_found record;
BEGIN
  -- проверка на существование сегмента таблицы
  SELECT relname INTO partition_found FROM pg_catalog.pg_class WHERE relname::text = partition_name;

  IF NOT found THEN
    -- создание сегмента таблицы с заполнением из нулевого города
    EXECUTE '
      CREATE TABLE aggregation.' || partition_name || '
      WITH (OIDS = true)
      AS SELECT * FROM aggregation.product_0
    ';
    EXECUTE '
      UPDATE aggregation.' || partition_name || '
      SET geo_city_id = ' || geo_city_id
    ;
    EXECUTE '
      ALTER TABLE aggregation.' || partition_name || '
      INHERIT public.product
    ';

    -- создание ограничения
    EXECUTE '
      ALTER TABLE aggregation.' || partition_name || '
      ADD CONSTRAINT ' || partition_name || '_geo_city_id_check
      CHECK (geo_city_id = ' || geo_city_id || ')
    ';

    -- создание первичного ключа
    EXECUTE '
      ALTER TABLE aggregation.' || partition_name || '
      ADD CONSTRAINT ' || partition_name || '_pkey PRIMARY KEY (id)
    ';

    -- создание уникального ключа
    EXECUTE '
      CREATE UNIQUE INDEX ' || partition_name || '_geo_city_id_base_product_id
      ON aggregation.' || partition_name || '
      USING BTREE (geo_city_id, base_product_id)
    ';

    -- создание индексов
    EXECUTE '
      CREATE INDEX ' || partition_name || '_product_availability_code_idx
      ON aggregation.' || partition_name || '
      USIN BTREE (product_availability_code pg_catalog.enum_ops)
    ';

    -- создание внешних ключей
    EXECUTE '
      ALTER TABLE aggregation.' || partition_name || '
      ADD CONSTRAINT ' || partition_name || '_base_product_id_fkey
      FOREIGN KEY (base_product_id)
      REFERENCES public.base_product(id)
      ON UPDATE NO ACTION
      ON DELETE CASCADE
    ';
    EXECUTE '
      ALTER TABLE aggregation.' || partition_name || '
      ADD CONSTRAINT ' || partition_name || '_geo_city_id_fkey
      FOREIGN KEY (geo_city_id)
      REFERENCES public.geo_city(id)
      ON UPDATE NO ACTION
      ON DELETE CASCADE
    ';
    EXECUTE '
      ALTER TABLE aggregation.' || partition_name || '
      ADD CONSTRAINT ' || partition_name || '_manual_price_operated_by_fkey
      FOREIGN KEY (manual_price_operated_by)
      REFERENCES public.user(id)
      ON UPDATE CASCADE
      ON DELETE RESTRICT
    ';
    EXECUTE '
      ALTER TABLE aggregation.' || partition_name || '
      ADD CONSTRAINT ' || partition_name || '_temporary_price_operated_by_fkey
      FOREIGN KEY (temporary_price_operated_by)
      REFERENCES public.user(id)
      ON UPDATE CASCADE
      ON DELETE RESTRICT
    ';
    EXECUTE '
      ALTER TABLE aggregation.' || partition_name || '
      ADD CONSTRAINT ' || partition_name || '_ultimate_price_operated_by_fkey
      FOREIGN KEY (ultimate_price_operated_by)
      REFERENCES public.user(id)
      ON UPDATE CASCADE
      ON DELETE RESTRICT
    ';

    -- создание триггера на добавление товара
    EXECUTE '
      CREATE TRIGGER product_after_insert_trigger
      AFTER INSERT
      ON aggregation.' || partition_name || '
      FOR EACH ROW
      EXECUTE PROCEDURE aggregation.product_after_insert_trigger()
    ';

    -- создание регистра обновления цен и наличия
    EXECUTE '
      CREATE TABLE aggregation.' || partition_update_register_name || ' (
        is_updated boolean NOT NULL DEFAULT 'f',
        CONSTRAINT ' || partition_update_register_name || '_pkey PRIMARY KEY (id)
      ) WITH (OIDS = true) INHERITS (public.product_update_register)
    ';

  END IF;
END
$BODY$
  LANGUAGE 'plpgsql' VOLATILE;