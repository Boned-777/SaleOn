CREATE TABLE `AdAddress` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ad_id` int(11) DEFAULT NULL,
  `address_id` text,
  PRIMARY KEY (`id`)
);

CREATE TABLE `PartnerAddress` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `name` text,
  PRIMARY KEY (`id`)
);
