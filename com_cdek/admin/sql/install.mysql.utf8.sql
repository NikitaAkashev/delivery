

DROP TABLE IF EXISTS `#__cdek_order_status`;

create table #__cdek_order_status(
	`order_status` int primary key auto_increment,
	`name` varchar(64),
	`code` varchar(32) unique
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `#__calc_order`;
 
create table #__cdek_order(
	`order` int primary key auto_increment,
	`user` int(11) NULL references `calc_users(id)`,
	`created` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
	`modified` TIMESTAMP,
    `order_status` int(11) references `#__calc_order_status(order_status)`,
    `outer_id` varchar(100)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


insert into #__cdek_order_status(name, code)
values ('Новый','new')

	
