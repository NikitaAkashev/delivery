
DROP TABLE IF EXISTS `#__calc_factor`;
 
CREATE TABLE `#__calc_factor` (
  `factor` int NOT NULL AUTO_INCREMENT,
  `name` varchar(32) NOT NULL unique,
  `value` decimal(4,2) not null,
   PRIMARY KEY  (`factor`)
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;
  
DROP TABLE IF EXISTS `#__calc_zone`;
 
CREATE TABLE `#__calc_zone` (
  `zone` int NOT NULL AUTO_INCREMENT,
  `name` varchar(32) NOT NULL unique,
   PRIMARY KEY  (`zone`)
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;
  
DROP TABLE IF EXISTS `#__calc_tariff`;
 
CREATE TABLE `#__calc_tariff` (
  `tariff` int NOT NULL AUTO_INCREMENT,
  `name` varchar(32) NOT NULL unique,
   PRIMARY KEY  (`tariff`)
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;
  

DROP TABLE IF EXISTS `#__calc_city`;
 
CREATE TABLE `#__calc_city` (
  `city` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(125) NOT NULL unique,
  `factor` int(11) not null references `#__calc_factor`(factor),
  `parent` int(11) null references `#__calc_city`(city),
   PRIMARY KEY  (`city`)
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
  `user` int(11) NULL references `#__users(id)`,
   PRIMARY KEY  (`city_from`, `city_to`),
   unique (`city_from`, `city_to`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
 

DROP TABLE IF EXISTS `#__calc_weight_price`;
 
CREATE TABLE `#__calc_weight_price` (
  `zone` int NOT NULL references `#__calc_zone`(zone),
  `tariff` int not null references `#__calc_tariff`(tariff),
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

set sql_mode='NO_AUTO_VALUE_ON_ZERO';
insert into `#__calc_zone`(`zone`, `name`)
values (0, 'Зона 0'),(null, 'Зона 1'),(null, 'Зона 2'),(null, 'Зона 3'),(null, 'Зона 4'),(null, 'Зона 5'),(null, 'Зона 6');
set sql_mode='';

insert into `#__calc_tariff`(`name`) 
values ('Стандарт (окно-окно)'), ('Стандарт (окно-дверь)'), ('Экспресс (окно-окно)'), ('Экспресс (окно-дверь)');

insert into `#__calc_factor`(`name`, `value`)
values ('Областной центр', 1.0), ('Районный центр', 1.25), ('Свердловская область', 1.15);

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
