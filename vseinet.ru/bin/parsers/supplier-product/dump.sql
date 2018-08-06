-- SQL dump. Product card parser.

DROP TABLE IF EXISTS data_supplier_product;
CREATE TABLE data_supplier_product (
    id int(11) NOT NULL,
    supplier_product_id int(11) NOT NULL,
    source_id int(11) NOT NULL,
    name varchar(255) NOT NULL, 
    code varchar(25) DEFAULT NULL, 
    ccode varchar(25) DEFAULT NULL, 
    artikul varchar(50) DEFAULT NULL,
    url varchar(1024) DEFAULT NULL,
    next_url varchar(1024) DEFAULT NULL,
    data json DEFAULT NULL,
    status smallint(6) DEFAULT 0,
    pending datetime DEFAULT NULL,
    complete datetime DEFAULT NULL,
    transfer datetime DEFAULT NULL,
    PRIMARY KEY (id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;