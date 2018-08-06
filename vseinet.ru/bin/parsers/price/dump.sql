-- SQL dump. Competitors price parser.

DROP TABLE IF EXISTS data_price;
CREATE TABLE data_price (
    id int(11) NOT NULL,
    base_product_id int(11) NOT NULL,
    source_id int(11) NOT NULL,
    url varchar(1024) NOT NULL,
    product_id int(11) NOT NULL,
    request tinyint(1) NOT NULL,
    price int(11) DEFAULT NULL,
    status smallint(6) DEFAULT NULL,
    pending datetime DEFAULT NULL,
    complete datetime DEFAULT NULL,
    transfer datetime DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
ALTER TABLE data_price 
    ADD PRIMARY KEY (id),
    ADD KEY source_id (source_id),
    ADD KEY request (request),
    ADD KEY pending (pending),
    ADD KEY complete (complete),
    ADD KEY transfer (transfer);