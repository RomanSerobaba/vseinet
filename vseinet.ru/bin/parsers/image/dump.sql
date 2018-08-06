-- SQL dump. Image product parser.

DROP TABLE IF EXISTS data_image;
CREATE TABLE data_image (
    id int(11) NOT NULL,
    base_product_id int(11) NOT NULL,
    source_id int(11) NOT NULL,
    url varchar(1024) NOT NULL,
    image_base_64 longblob DEFAULT NULL,
    status smallint(6) DEFAULT 0,
    pending datetime DEFAULT NULL,
    complete datetime DEFAULT NULL,
    transfer datetime DEFAULT NULL,
    PRIMARY KEY (id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;