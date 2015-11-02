create table `calc_delivery_tariff` (
	`tariff` int NOT NULL AUTO_INCREMENT,
	`name` varchar(255) not null,
	`code` varchar(63) not null,
	`margin` decimal(4,2) not null,
	dimension_limit int(11) null,
	weight_limit int(11) null,
	oversize_limit_factor decimal(4,2),
	PRIMARY KEY  (`tariff`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


create table `calc_delivery_provider` (
	`provider` int(11) NOT NULL AUTO_INCREMENT,
	`name` varchar(125) NOT NULL,
	`code` varchar(125) NOT NULL,
	`volume_weight_divider` int(11) not null,
	min_assessed_price decimal(15,2),
	is_zones_by_exact_city tinyint(1) default 1,
	prices_with_nds tinyint(1) default 0,
	PRIMARY KEY  (`provider`),
	UNIQUE (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

create table `calc_delivery_delivery_type` (
	`delivery_type` int(11) NOT NULL AUTO_INCREMENT,
	`name` varchar(125) NOT NULL,
	`from_office` tinyint(1) not null,
	`to_office` tinyint(1) not null,
	`code` varchar(63) not null,
	PRIMARY KEY  (`delivery_type`),
	UNIQUE (`from_office`, `to_office`),
	UNIQUE (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


create table `calc_delivery_city` (
	`city` int(11) NOT NULL AUTO_INCREMENT,
	`name` varchar(125) NOT NULL,
	`parent` int(11) null references `calc_calc_city`(city),
	`region_name` varchar(127) null,
	PRIMARY KEY  (`city`),
	UNIQUE (`name`, `parent`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `calc_delivery_zone` (
	`zone` int NOT NULL AUTO_INCREMENT,
	`name` varchar(32) NOT NULL,
	`code` varchar(32) NOT NULL unique,
	`provider` int(11) NOT NULL references `calc_delivery_provider(provider)`,
	PRIMARY KEY  (`zone`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `calc_delivery_direction2zone` (
	`city_from` int(11) NOT NULL references `calc_delivery_city`(city),
	`city_to` int(11) NOT NULL references `calc_delivery_city`(city),
	`zone` int not null references `calc_delivery_zone`(zone),
	min_days int(11) null,
	max_days int(11) null,
	PRIMARY KEY  (`zone`,`city_from`,`city_to`),
	unique  (`zone`,`city_from`,`city_to`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `calc_delivery_assessed_value_price` (
	`from` decimal(15,2) NOT NULL,
	`to` decimal(15,2) NOT NULL,
	`base_price` decimal(15,2) not null,
	`overprice_percent` decimal(15,10) not null default 0,
	`tariff` int(11) not null references `calc_delivery_tariff`(tariff)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


create TABLE `calc_delivery_discount` (
	`discount` int(11) NOT NULL AUTO_INCREMENT primary key,
	`city_from` int(11) NULL references `calc_delivery_city`(city),
	`city_to` int(11) NULL references `calc_delivery_city`(city),
	`tariff` int(11) NULL references `calc_delivery_tariff`(tariff),
	`factor` decimal(4,2) not null,
	`user` int(11) NULL references `calc_users(id)`,
	unique  (`city_from`, `city_to`, `tariff`, `user`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `calc_delivery_courier_price` (
  `courier_price` int NOT NULL AUTO_INCREMENT,
	`tariff` int(11) not null references `calc_delivery_tariff`(tariff),
	`weight_from` decimal(15,2) not null,
	`weight_to` decimal(15,2) not null,
	`price` decimal(15,2) not null,
	PRIMARY KEY  (`courier_price`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


create table `calc_delivery_city2delivery_time`(
	`city` int(11) NOT NULL references `calc_delivery_city`(city),
	min_time int(11) not null,
	max_time int(11) not null,
	`provider` int(11) NOT NULL references `calc_delivery_provider`
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


create table `calc_delivery_city_factor`(
	`city` int(11) NOT NULL references `calc_delivery_city`(city),
	factor_inner decimal(4,2) not null default 1,
	factor_outer decimal(4,2) not null default 1,
	`tariff` int(11) NOT NULL references `calc_delivery_tariff`
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


create TABLE `calc_delivery_rate` (
	`rate` int(11) not null auto_increment,
	`city_from` int(11) NULL references `calc_delivery_city`(city),
	`city_to` int(11) NULL references `calc_delivery_city`(city),
	`zone` int(11) null references `calc_delivery_zone`(zone),
	`tariff` int(11) not null references `calc_delivery_tariff`(tariff),
	`provider` int(11) not null references `calc_delivery_provider`(provider),
	`min_days` int(11) null,
	`max_days` int(11) null,
	`delivery_hours` varchar(32) null ,
	`is_enabled` int(1) not null default 0,
	PRIMARY KEY (`rate`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `calc_delivery_weight_price` (
	`rate` int NOT NULL references `calc_delivery_rate`(rate),
	`from` decimal(10,2) NOT NULL,
	`to` decimal(10,2) NOT NULL,
	`base_price` decimal(15,2) not null,
	`overweight_cost` decimal(15,2) not null default 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


create table calc_delivery_delivery_type2tariff(
	`delivery_type` int(11) not null references `calc_delivery_delivery_type`(delivery_type),
	`tariff` int(11) not null references `calc_delivery_tariff`(tariff)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


create table calc_delivery_order(
	`order` int primary key auto_increment,
	`user` int(11) NULL references `calc_users(id)`,
	`created` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
	`modified` TIMESTAMP,
	`price` decimal(15,2) not null,
	`calc_row_id` varchar(64),
	`rate` int(11) not null references `calc_delivery_rate(rate)`,
	`delivery_type_code` varchar(64),
    `city_from` int(11) not null references `calc_delivery_city(city)`,
    `city_to` int(11) not null references `calc_delivery_city(city)`,
    `weight` float not null,
    `assessed_value` float,
    `width` float not null,
    `length` float not null,
    `height` float not null,
    `customer_name` varchar(1024),
    `email` varchar(1024),
    `phone` varchar(1024),
    `comments` varchar(1024)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


create table calc_delivery_settings(
	`settings` int primary key auto_increment,
	`value` varchar(1024) not null,
	`code` varchar(64) not null,
	UNIQUE(`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `calc_delivery_city2provider` (
  `city` int(11) NOT NULL references calc_delivery_city(city),
  `provider` int(11) NOT NULL references calc_delivery_provider(provider),
  PRIMARY KEY (`city`,`provider`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


create table if not exists `calc_delivery_user`(
	`user` int not null primary key references `calc_users(id)` ,
	`contract_name` varchar(64) not null,
	unique(`contract_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


create table if not exists `calc_delivery_parcel`(
	`parcel` int primary key auto_increment,
	`is_enabled` tinyint(1) not null default 1,
	`owner` int(11) NULL references `calc_users(id)`,
	`creator` int(11) not NULL references `calc_users(id)`,
	`parcel_number` varchar(64) not null,
	`created` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
	`sender` varchar(1024) not null,
	`receiver` varchar(1024) not null,
	`payer` varchar(1024) not null,
	`address_from` varchar(1024) not null,
	`address_to` varchar(1024) not null,
	`mem` varchar(4096) null,
	UNIQUE(`parcel_number`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

create table if not exists `calc_delivery_parcel_status`(
	`parcel_status` int primary key auto_increment,
	`name` varchar(32) not null,
	`code` varchar(32) not null,
	UNIQUE(`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


create table if not exists `calc_delivery_parcel2parcel_status`(
	`parcel2parcel_status` int primary key auto_increment,
	`parcel` int not null references `calc_delivery_parcel(parcel)`,
	`parcel_status` int not null references `calc_delivery_parcel_status(parcel_status)`,
	`dt` timestamp not null default current_timestamp,
	UNIQUE(`parcel`, `parcel_status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

insert into calc_delivery_parcel_status(name, code)
values 
	('принято', 'taken'), 
	('отправлено', 'sent'), 
	('на скалде', 'stock'),
	('готов к выдаче', 'ready'),
	('врученно', 'executed');

insert into calc_delivery_settings(value, code)
values ('regspambox@yandex.ru','mail_to'), ('regspambox@yandex.ru','mail_from'), ('Заказ с сайта','mail_subject');

insert into calc_delivery_tariff(name, code, margin, weight_limit,
dimension_limit , oversize_limit_factor)
values ('Экспресс-Стандарт', 'standart', 1.3, 300, 200, 1.5),
('СуперЭкспресс-Стандарт', 'super', 1.3, 20, 150, 0),
('Экспресс-Урал', 'ural', 1.3, null, 150, 1.3),
('Экспресс-Приоритет', 'priority', 1.3, 30, 150, 0);

insert into `calc_delivery_provider` (	`name`,	`code`,	`volume_weight_divider`, `min_assessed_price`, `is_zones_by_exact_city`, `prices_with_nds` )
values ('СпецСвязь', 'special', 6000, 0, 0, 0),
 ('СДЭК', 'cdek', 5000, 400, 1, 0),
 ('ФОКС', 'fox', 5000, 400, 1, 1);

insert into calc_delivery_delivery_type (name, from_office, to_office, code)
values 
	('Дверь-дверь', 0, 0, 'door.door'),
	('Окно-дверь', 1, 0, 'office.door');

insert into calc_delivery_city(name, parent, region_name)
select name, parent, region_name from calc_calc_city;

set sql_mode='NO_AUTO_VALUE_ON_ZERO';

insert into calc_delivery_zone(zone, name, code, provider)
select z.zone, z.name, concat(p.code,'.',cast(z.zone as char)), p.provider
 from calc_calc_zone z 
	join calc_delivery_provider p on p.code='special';

set sql_mode='';

insert into calc_delivery_direction2zone(city_from, city_to, zone)
select city_from, city_to, zone from calc_calc_direction2zone;

insert into calc_delivery_assessed_value_price (`from`, `to`, base_price, overprice_percent, tariff)
select 
	c.from,
	c.to,
	c.base_price,
	c.overprice_percent,
	t.tariff
from calc_calc_assessed_value_price c
	join calc_delivery_tariff t on t.code = 'standart'
where
	c.is_public = 0;


insert into calc_delivery_assessed_value_price (`from`, `to`, base_price, overprice_percent, tariff)
select 
	0, 100000000, 0, 0.0011, t.tariff
from calc_delivery_tariff t
where t.code in ('super', 'priority');

#select * from calc_delivery_tariff where code = 'ural'
#> 3
insert into calc_delivery_assessed_value_price (`from`, `to`, base_price, overprice_percent, tariff)
values 
	(0, 50000, 0, 0.0011, 3),
	(50000, 100000, 0, 0.001, 3),
	(100000, 150000, 0, 0.0009, 3),
	(150000, 300000, 0, 0.0008, 3),
	(300000, 500000, 0, 0.0007, 3),
	(500000, 1000000, 0, 0.0006, 3),
	(1000000, 100000000, 0, 0.0005, 3);

insert into calc_delivery_discount(city_from, city_to, factor, user)
select city_from, city_to, factor, user from calc_calc_discount;


insert into calc_delivery_courier_price(tariff, weight_from, weight_to, price)
select t.tariff, 0, 10000, 150 
from calc_delivery_tariff t;


insert into calc_delivery_city2delivery_time(city, min_time, max_time, provider)
select c.city, c.express_min_delivery_time, c.express_max_delivery_time, p.provider
from calc_calc_city c
	join calc_delivery_provider p on p.code='special';

insert into calc_delivery_city_factor (city, factor_inner, factor_outer, tariff)
select 
	c.city, f.value_for_inner_calculations, f.value - f.value_for_inner_calculations + 1, t.tariff
from calc_calc_city c
	join calc_calc_factor f on f.factor = c.factor
	join calc_delivery_tariff t on t.code = 'standart';

insert into calc_delivery_city_factor(city, tariff, factor_outer, factor_inner)
select c.city, t.tariff, 1.1, 1
from calc_delivery_city c
	join calc_delivery_tariff t on t.code = 'ural'
where 
	 c.parent in (68, 31, 47, 18, 65, 20, 72);/*Уфа, Курган, Пермь, Екб, Тюмень, Ижевск, Че*/


insert into calc_delivery_rate(zone, tariff, provider)
select 
	z.zone, t.tariff, p.provider
from calc_delivery_zone z
	join calc_delivery_tariff t on t.code = 'standart'
	join calc_delivery_provider p on p.code='special';

insert into calc_delivery_weight_price (rate, `from`, `to`, base_price, overweight_cost)
select 
	r.rate, owp.`from`, owp.`to`, owp.base_price, owp.overweight_cost
from calc_calc_weight_price owp 
	join calc_calc_tariff ot on ot.tariff = owp.tariff
	join calc_delivery_rate r on 
		r.zone = owp.zone
		and ot.tariff = 6;

insert into calc_delivery_delivery_type2tariff(delivery_type, tariff)
select dt.delivery_type, t.tariff
from calc_delivery_tariff t
	cross join calc_delivery_delivery_type dt
where
	t.code <> 'super';

insert into calc_delivery_delivery_type2tariff(delivery_type, tariff)
select dt.delivery_type, t.tariff
from calc_delivery_tariff t
	cross join calc_delivery_delivery_type dt
where
	t.code = 'super'
	and dt.code = 'door.door';


insert into calc_delivery_city2provider(city, provider)
select c.city, p.provider 
from calc_delivery_city c
	join calc_delivery_provider p on p.code = 'special'
where c.city < 2115 -- Все, кто для спецсвязи
