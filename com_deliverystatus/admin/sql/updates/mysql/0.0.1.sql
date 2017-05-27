

    alter table `#__delivery_parcel` 
		add column `outer_id` varchar(64) null;


	update `#__delivery_parcel_status` set code = 3, name = 'Принят на склад отправителя' where code = 'taken';
	update `#__delivery_parcel_status` set code = 7, name = 'Сдан перевозчику в г. отправителе' where code = 'sent';
	update `#__delivery_parcel_status` set code = 10, name = 'Принят на склад доставки' where code = 'stock';
	update `#__delivery_parcel_status` set code = 12, name = 'Принят на склад до востребования' where code = 'ready';
	update `#__delivery_parcel_status` set code = 4, name = 'Вручен' where code = 'executed';
	
	
	insert into `#__delivery_parcel_status`(name, code)
	values	('Создан', 1),
			('Удален', 2),
			('Не вручен', 5),
			('Выдан на отправку в г. отправителе', 6),
			('Возвращен на склад отправителя', 16),
			('Отправлен в г. транзит', 21),
			('Встречен в г. транзите', 22),
			('Принят на склад транзита', 13),
			('Возвращен на склад транзита', 17),
			('Выдан на отправку в г. транзите', 19),
			('Сдан перевозчику в г. транзите', 20),
			('Отправлен в г. получатель', 8),
			('Встречен в г.получателе', 9),
			('Выдан на доставку', 11),
			('Возвращен на склад доставки', 18);
