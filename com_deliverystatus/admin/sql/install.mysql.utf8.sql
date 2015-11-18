
create table if not exists `#__delivery_parcel`(
	`parcel` int primary key auto_increment,
	`is_enabled` tinyint(1) not null default 1,
	`owner` int(11) NULL references `calc_users(id)`,
	`creator` int(11) not NULL references `calc_users(id)`,
	`parcel_number` varchar(64) not null,
	`created` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
	`sender` varchar(1024) not null,
	`receiver` varchar(1024) not null,
	`payer` varchar(1024) null,
	`address_from` varchar(1024) not null,
	`address_to` varchar(1024) not null,
	`mem` varchar(4096) null,
	UNIQUE(`parcel_number`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

create table if not exists `#__delivery_parcel_status`(
	`parcel_status` int primary key auto_increment,
	`name` varchar(32) not null,
	`code` varchar(32) not null,
	UNIQUE(`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


create table if not exists `#__delivery_parcel2parcel_status`(
	`parcel2parcel_status` int primary key auto_increment,
	`parcel` int not null references `calc_delivery_parcel(parcel)`,
	`parcel_status` int not null references `calc_delivery_parcel_status(parcel_status)`,
	`dt` timestamp not null default current_timestamp,
	UNIQUE(`parcel`, `parcel_status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

insert into #__delivery_parcel_status(name, code)
values 
	('принято', 'taken'), 
	('отправлено', 'sent'), 
	('на скалде', 'stock'),
	('готов к выдаче', 'ready'),
	('врученно', 'executed');

insert into #__delivery_parcel(
-	`creator`,
-	`parcel_number`,
-	`created` ,
-	`sender` ,
-	`receiver` ,
-	`address_from`,
-	`address_to` ,
-	`mem`)
-select 
-	created_by,
-	alias,
-	created,
-	SUBSTRING_INDEX(title,'—',1),
-	SUBSTRING_INDEX(title,'—',-1),
-	napravlenie_from,
-	napravlenie_to,
-	komentariy
-from ejtsu_status;
-
-
-insert into calc_delivery_parcel2parcel_status(parcel, parcel_status)
-select p.parcel, ps.parcel_status
-from #__delivery_parcel p
-	join ejtsu_status s on s.alias = p.parcel_number
-	join #__delivery_parcel_status ps on
-		ps.code = case s.statusname
-				when 'Получатель получил груз' then 'executed'
-				when 'Принято к отправке' then 'taken'
-				when 'Груз в точке получения' then 'ready'
-				when 'Груз отправлен получателю' then 'sent'
-			end
