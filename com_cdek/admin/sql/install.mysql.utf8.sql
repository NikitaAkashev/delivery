
DROP TABLE IF EXISTS `#__cdek_order`;
DROP TABLE IF EXISTS `#__cdek_settings`;
drop table if exists `#__cdek_tariff`;


create table #__cdek_order(
	`order` int primary key auto_increment,
	`created` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
	`modified` TIMESTAMP,
    `outer_tariff_id` int,
    `outer_tariff_name` varchar(64),
    `outer_city_from_id` int,
    `outer_city_from_name` varchar(128),
    `outer_city_to_id` int,
    `outer_city_to_name` varchar(128),
    `weight` decimal(15,4),
    `width` decimal(15,4),
    `height` decimal(15,4),
    `length` decimal(15,4),
    `price` decimal(15,4),
    `customer_name` varchar(64),
    `email` varchar(128),
    `phone` varchar(20),
    `mem` varchar(1024)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


create table #__cdek_settings(
	`settings` int primary key auto_increment,
	`mail_to` varchar(64) not null,
	`mail_from` varchar(64) not null,
	`mail_subject` varchar(128) not null,
	`interest` decimal(4,2) not null
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


create table #__cdek_tariff(
	tariff int(11) primary key auto_increment,
	tariff_id int(11) not null,
	tariff_name varchar(64) unique not null,
	published int(4)
);

insert into #__cdek_settings(mail_to, mail_from, mail_subject, interest)
values ('regspambox@yandex.ru', 'regspambox@yandex.ru', 'Заказ с сайта', 1.3);
