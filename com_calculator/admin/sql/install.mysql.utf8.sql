
DROP TABLE IF EXISTS `#__calc_factor`;
 
CREATE TABLE `#__calc_factor` (
  `factor` int NOT NULL AUTO_INCREMENT,
  `name` varchar(32) NOT NULL unique,
  `value` decimal(4,2) not null,
  `value_for_inner_calculations` decimal(4,2) not null,
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
  `is_public` tinyint(1) not null,
  `is_express` tinyint(1) not null,
  `from_door` tinyint(1) not null,
   PRIMARY KEY  (`tariff`)
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;
  

DROP TABLE IF EXISTS `#__calc_city`;
 
CREATE TABLE `#__calc_city` (
  `city` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(125) NOT NULL,
  `factor` int(11) not null references `#__calc_factor`(factor),
  `parent` int(11) null references `#__calc_city`(city),
  `express_min_delivery_time` int not null, 
  `express_max_delivery_time` int not null, 
  `standart_min_delivery_time` int not null, 
  `standart_max_delivery_time` int not null, 
  `region_name` varchar(127) null,
   PRIMARY KEY  (`city`),
   UNIQUE (`name`, `parent`)
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `#__calc_terminal`;
 
CREATE TABLE `#__calc_terminal` (
  `terminal` int NOT NULL AUTO_INCREMENT,
  `name` varchar(127) NOT NULL,
  `city` int not null references `#__calc_city`(city),
   PRIMARY KEY  (`terminal`)
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
  `overprice_percent` decimal(15,10) not null default 0,
  `is_public` tinyint(1) not null
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `#__calc_order_status`;

create table #__calc_order_status(
	`order_status` int primary key auto_increment,
	`name` varchar(64),
	`code` varchar(32) unique
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `#__calc_order`;
 
create table #__calc_order(
	`order` int primary key auto_increment,
	`user` int(11) NULL references `calc_users(id)`,
	`created` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
	`modified` TIMESTAMP,
	`price` decimal(15,2) not null,
	`tariff` int(11) not null references `#__calc_tariff(tariff)`,
    `city_from` int(11) not null references `#__calc_city(city)`,
    `city_to` int(11) not null references `#__calc_city(city)`,
    `from_door` tinyint(1) not null,
    `from_terminal` int(11) null references `#__calc_terminal(terminal)`,
    `from_door_street`varchar(127) null,
    `from_door_house` varchar(32) null,
    `from_door_building` varchar (32) null,
    `from_door_structure` varchar(32) null,
    `from_door_flat` varchar(32) null,
    `from_door_worktime_start` varchar(10),
    `from_door_worktime_end`  varchar(10),
    `from_door_breaktime_start`  varchar(10),
    `from_door_breaktime_end`  varchar(10),
    `from_door_exact_time` tinyint(1),
    `to_door` tinyint(1) not null,
    `to_terminal` int(11) null references `#__calc_terminal(terminal)`,
    `to_door_street` varchar(127),
    `to_door_house` varchar(32),
    `to_door_building` varchar(32),
    `to_door_structure` varchar(32),
    `to_door_flat` varchar(32),
    `to_door_worktime_start` varchar(10),
    `to_door_worktime_end` varchar(10),
    `to_door_breaktime_start` varchar(10),
    `to_door_breaktime_end` varchar(10),
    `to_door_exact_time` tinyint(1),
    `weight` float not null,
    `assessed_value` float,
    `width` float not null,
    `length` float not null,
    `height` float not null,
    `produceDate` varchar(32),
    `comments` varchar(1024),
    `sender_legal_type` varchar(32),
    `sender_inn` varchar(32),
    `sender_company_name` varchar(127),
    `sender_name` varchar(127),
    `sender_ZIP_code` varchar(32),
    `sender_juridical_city` varchar(127),
    `sender_juridical_street` varchar(127),
    `sender_house` varchar(32),
    `sender_building` varchar(32),
    `sender_structure` varchar(32),
    `sender_flat` varchar(32),
    `sender_contact` varchar(127),
    `sender_phone` varchar(32),
    `receiver_legal_type` varchar(32),
    `receiver_inn` varchar(32),
    `receiver_company_name` varchar(127),
    `receiver_name` varchar(127),
    `receiver_ZIP_code` varchar(127),
    `receiver_juridical_city` varchar(127),
    `receiver_juridical_street` varchar(127),
    `receiver_house` varchar(32),
    `receiver_building` varchar(32),
    `receiver_structure` varchar(32),
    `receiver_flat` varchar(32),
    `receiver_contact` varchar(127),
    `receiver_phone` varchar(32),
    `payer` varchar(32),
    `third_inn` varchar(32),
    `third_company_name` varchar(127),
    `third_ZIP_code` varchar(32),
    `third_juridical_city` varchar(127),
    `third_juridical_street` varchar(127),
    `third_house` varchar(32),
    `third_building` varchar(32),
    `third_structure` varchar(32),
    `third_flat` varchar(32),
    `third_contact` varchar(127),
    `third_phone` varchar(32),
    `order_status` int(11) references `#__calc_order_status(order_status)`,
    `outer_id` varchar(100)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


set sql_mode='NO_AUTO_VALUE_ON_ZERO';
insert into `#__calc_zone`(`zone`, `name`)
values (0, 'Зона 0'),(null, 'Зона 1'),(null, 'Зона 2'),(null, 'Зона 3'),(null, 'Зона 4'),(null, 'Зона 5'),(null, 'Зона 6');
set sql_mode='';

insert into #__calc_order_status(name, code)
values ('Новый','new')

insert into `#__calc_tariff`(`name`, `is_public`, `is_express`, `from_door`) 
values 
	('Дверь-Дверь (Экспресс)', 1, 1, 1), 
	('Окно-Дверь (Экспресс)', 1, 1, 0), 
	('Дверь-Дверь (Стандарт)', 1, 0, 1), 
	('Окно-Дверь (Стандарт)', 1, 0, 0),
	('Дверь-Дверь (Внутренний)', 0, 0, 1),
	('Окно-Дверь (Внутренний)', 0, 0, 0);

insert into `#__calc_factor`(`name`, `value`, `value_for_inner_calculations`)
values ('Областной центр', 1.0, 1.0), ('Районный центр', 1.3, 1.25), ('Свердловская область', 1.2, 1.15);

insert into `#__calc_assessed_value_price`(`from`, `to`, `base_price`, `overprice_percent`, `is_public`)
values
	(0,1,0,0, 1),
	(1,15000,250,0, 1),
	(15000,25000,600,0, 1),
	(25000,50000,1250,0, 1),
	(50000,150000,1250,0.012, 1),
	(150000,500000,2300,0.01, 1),
	(500000,1000000,4100,0.005, 1),
	(1000000,2000000,5500,0.003, 1),
	(2000000,5000000,7500,0.0025, 1),
	(5000000,10000000,12000,0.0015, 1),
	(10000000,20000000,17000,0.0007, 1),
	(20000000,100000000,22000,0.0005, 1),
	(0,1,0,0, 0),
	(1,15000,200,0, 0),
	(15000,25000,500,0, 0),
	(25000,50000,1000,0, 0),
	(50000,150000,1000,0.01, 0),
	(150000,500000,2000,0.005, 0),
	(500000,1000000,3750,0.003, 0),
	(1000000,2000000,5250,0.002, 0),
	(2000000,5000000,7250,0.0015, 0),
	(5000000,10000000,11750,0.001, 0),
	(10000000,20000000,16750,0.0005, 0),
	(20000000,100000000,21750,0.0004, 0);
	
