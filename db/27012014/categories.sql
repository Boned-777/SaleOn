DROP TABLE IF EXISTS categories;
CREATE TABLE categories (
  id int(11) NOT NULL AUTO_INCREMENT,
  parent int(11) DEFAULT NULL,
  name varchar(45) DEFAULT NULL,
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO categories VALUES (1,0,'cat_goods');
INSERT INTO categories VALUES (2,0,'cat_services');
INSERT INTO categories VALUES (3,1,'goods_transport');
INSERT INTO categories VALUES (4,1,'goods_building');
INSERT INTO categories VALUES (5,1,'goods_children');
INSERT INTO categories VALUES (6,1,'goods_food');
INSERT INTO categories VALUES (7,1,'goods_clothing');
INSERT INTO categories VALUES (8,1,'goods_electronic');
INSERT INTO categories VALUES (9,1,'goods_furniture');
INSERT INTO categories VALUES (10,1,'goods_dishes');
INSERT INTO categories VALUES (11,1,'goods_health');
INSERT INTO categories VALUES (12,1,'goods_equipment');
INSERT INTO categories VALUES (13,1,'goods_sport');
INSERT INTO categories VALUES (14,1,'goods_hygiene');
INSERT INTO categories VALUES (15,1,'goods_other_products');
INSERT INTO categories VALUES (16,2,'serv_transport');
INSERT INTO categories VALUES (17,2,'serv_construction');
INSERT INTO categories VALUES (18,2,'serv_entertainment');
INSERT INTO categories VALUES (19,2,'serv_health');
INSERT INTO categories VALUES (20,2,'serv_household');
INSERT INTO categories VALUES (21,2,'serv_financial');
INSERT INTO categories VALUES (22,2,'serv_education');
INSERT INTO categories VALUES (23,2,'serv_insurance');
INSERT INTO categories VALUES (24,2,'serv_telecommunication');
INSERT INTO categories VALUES (25,2,'serv_legal');
INSERT INTO categories VALUES (26,2,'serv_travel');
INSERT INTO categories VALUES (27,2,'serv_advertising');
INSERT INTO categories VALUES (28,2,'serv_other_services');