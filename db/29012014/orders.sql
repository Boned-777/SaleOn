CREATE TABLE orders (
  id int(11) NOT NULL AUTO_INCREMENT,
  status varchar(45) DEFAULT NULL,
  ad varchar(45) DEFAULT NULL,
  type varchar(45) DEFAULT NULL,
  amount float DEFAULT NULL,
  created_dt datetime DEFAULT NULL,
  paid_dt datetime DEFAULT NULL,
  modified_dt datetime DEFAULT NULL,
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;