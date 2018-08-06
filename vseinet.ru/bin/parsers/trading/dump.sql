-- SQL dump. Trading points parser.

DROP TABLE IF EXISTS data_trading;
CREATE TABLE data_trading (
  id int(11) NOT NULL,
  name varchar(255) DEFAULT NULL,
  url varchar(1024) DEFAULT NULL,
  status smallint(6) DEFAULT NULL,
  pending datetime DEFAULT NULL,
  complete datetime DEFAULT NULL,
  transfer datetime DEFAULT NULL,
  points text DEFAULT NULL,
  PRIMARY KEY (id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;