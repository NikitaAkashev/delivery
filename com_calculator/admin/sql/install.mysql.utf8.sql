DROP TABLE IF EXISTS `#__calc_city`;
 
CREATE TABLE `#__calc_city` (
  `city` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(125) NOT NULL unique,
  `factor` decimal(4,2) not null,
  `parent` int(11) null references `#__calc_city`(city),
   PRIMARY KEY  (`city`)
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `#__calc_zone`;
 
CREATE TABLE `#__calc_zone` (
  `zone` int NOT NULL AUTO_INCREMENT,
  `name` varchar(32) NOT NULL unique,
   PRIMARY KEY  (`zone`)
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;
  
 
DROP TABLE IF EXISTS `#__calc_direction2zone`;
 
CREATE TABLE `#__calc_direction2zone` (
  `city_from` int(11) NOT NULL references `#__calc_city`(city),
  `city_to` int(11) NOT NULL references `#__calc_city`(city),
  `zone` int not null references `#__calc_zone`(zone),
   PRIMARY KEY  (`zone`,`city_from`,`city_to`),
   unique  (`zone`,`city_from`,`city_to`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
 

DROP TABLE IF EXISTS `#__calc_discount`;
 
CREATE TABLE `#__calc_discount` (
  `city_from` int(11) NOT NULL references `#__calc_city`(city),
  `city_to` int(11) NOT NULL references `#__calc_city`(city),
  `factor` decimal(4,2) not null,
  `user` int(11) NULL references `#__users(id)`
   PRIMARY KEY  (`city_from`, `city_to`),
   unique (`city_from`, `city_to`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
 

DROP TABLE IF EXISTS `#__calc_weight_price`;
 
CREATE TABLE `#__calc_weight_price` (
  `zone` int NOT NULL references `#__calc_zone`(zone),
  `from` decimal(10,2) NOT NULL,
  `to` decimal(10,2) NOT NULL,
  `base_price` decimal(15,2) not null,
  `overweight_cost` decimal(15,2) not null default 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `#__calc_assessed_value_price`;
 
CREATE TABLE `#__calc_assessed_value_price` (
  `from` decimal(15,2) NOT NULL,
  `to` decimal(15,2) NOT NULL,
  `base_price` decimal(15,2) not null,
  `overprice_percent` decimal(15,10) not null default 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

insert into `#__calc_zone`(`name`)
values ('Зона 1'),('Зона 2'),('Зона 3'),('Зона 4'),('Зона 5'),('Зона 6');

insert into `#__calc_weight_price`(`zone`, `from`, `to`, `base_price`, `overweight_cost`)
values	(3, 0, 0.5, 429, 0),
		(3, 0.5, 1, 464, 0),
		(3, 1, 2, 562, 0),
		(3, 2, 3, 636, 0),
		(3, 3, 4, 710, 0),
		(3, 4, 5, 783, 0),
		(3, 5, 6, 847, 0),
		(3, 6, 7, 911, 0),
		(3, 7, 8, 975, 0),
		(3, 8, 9, 1039, 0),
		(3, 9, 10, 1103, 0),
		(3, 10, 20, 1103, 62),
		(3, 20, 100, 1737, 51),
		(3, 100, 10000, 5817, 35);

insert into `#__calc_assessed_value_price`(`from`, `to`, `base_price`, `overprice_percent`)
values	(0,15000,200,0),
		(15000,25000,500,0),
		(25000,50000,1000,0),
		(50000,150000,1000,0.01),
		(150000,500000,2000,0.005),
		(500000,1000000,3750,0.003),
		(1000000,2000000,5250,0.002),
		(2000000,5000000,7250,0.0015),
		(5000000,10000000,11750,0.001),
		(10000000,20000000,16750,0.0005),
		(20000000,100000000,21750,0.0004);








