<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
?>
<html>
	<head>
		<title>Заказ с Сайта</title>
	</head>
	<body>
		<p>Тариф: <?php echo ($this->pit->from_door ? 'Дверь-Дверь ' : 'Окно-Дверь ').($this->pit->is_express ? 'Экспресс' : 'Стандарт'); ?></p>
		<p>
			Откуда: <?php echo $this->extra->city_name_from->name; ?>, 
			<?php if ($this->data->from_door) { ?>
				ул. <?php echo $this->data->from_door_street; ?>
				д. <?php echo $this->data->from_door_house; ?>
				<?php echo (empty($this->data->from_door_building) ? '' : ', корп. '.$this->data->from_door_building); ?>
				<?php echo (empty($this->data->from_door_structure) ? '' : ', стр. '.$this->data->from_door_structure); ?>
				<?php echo (empty($this->data->from_door_flat) ? '' : ', кв/оф. '.$this->data->from_door_flat); ?><br />
				Время забора:
					<?php echo (empty($this->data->from_door_worktime_start) ? '' : 'с '.$this->data->from_door_worktime_start); ?>
					<?php echo (empty($this->data->from_door_worktime_end) ? '' : ' до '.$this->data->from_door_worktime_end); ?>
					<?php echo (empty($this->data->from_door_breaktime_start) && empty($this->data->from_door_breaktime_end) ? '' : ' перерыв' ); ?>
					<?php echo (empty($this->data->from_door_breaktime_start) ? '' : ' с '.$this->data->from_door_breaktime_start); ?>
					<?php echo (empty($this->data->from_door_breaktime_end) ? '' : ' до '.$this->data->from_door_breaktime_end); ?>
					<?php echo (
						empty($this->data->from_door_worktime_start) 
						&& empty($this->data->from_door_worktime_end)
						&& empty($this->data->from_door_breaktime_start)
						&& empty($this->data->from_door_breaktime_end)						
							? 'не указано' : '' ); ?>				
				<?php echo $this->data->from_door_exact_time ? 'Фиксированное время забора' : '' ?><br />
			<?php } else { ?>
				терминал  <?php echo $this->extra->terminal_from->name; ?>
			<?php } ?>
		</p>
		<p>
			Куда: <?php echo $this->extra->city_name_to->name; ?>,
			<?php if ($this->data->to_door) { ?>
				ул. <?php echo $this->data->to_door_street; ?>
				д. <?php echo $this->data->to_door_house; ?>
				<?php echo (empty($this->data->to_door_building) ? '' : ', корп. '.$this->data->to_door_building); ?>
				<?php echo (empty($this->data->to_door_structure) ? '' : ', стр. '.$this->data->to_door_structure); ?>
				<?php echo (empty($this->data->to_door_flat) ? '' : ', кв/оф. '.$this->data->to_door_flat); ?><br />
				Время доставки: 
					<?php echo (empty($this->data->to_door_worktime_start) ? '' : 'с '.$this->data->to_door_worktime_start); ?>
					<?php echo (empty($this->data->to_door_worktime_end) ? '' : ' до '.$this->data->to_door_worktime_end); ?>
					<?php echo (empty($this->data->to_door_breaktime_start) && empty($this->data->to_door_breaktime_end) ? '' : ' перерыв' ); ?>
					<?php echo (empty($this->data->to_door_breaktime_start) ? '' : ' с '.$this->data->to_door_breaktime_start); ?>
					<?php echo (empty($this->data->to_door_breaktime_end) ? '' : ' до '.$this->data->to_door_breaktime_end); ?>
					<?php echo (
						empty($this->data->to_door_worktime_start) 
						&& empty($this->data->to_door_worktime_end)
						&& empty($this->data->to_door_breaktime_start)
						&& empty($this->data->to_door_breaktime_end)						
							? 'не указано' : '' ); ?>							
				<?php echo $this->data->to_door_exact_time ? 'Фиксированное время доставки' : '' ?><br />
			<?php } else { ?>
				терминал  <?php echo $this->extra->terminal_from->name; ?>
			<?php } ?>
		</p>
		<p>
			Вес: <?php echo $this->data->weight; ?> кг	<br />
			Оценка: <?php echo $this->data->assessed_value; ?> руб	<br />
			Ширина: <?php echo $this->data->width; ?> cм	<br />
			Длина: <?php echo $this->data->length; ?> см	<br />
			Высота: <?php echo $this->data->height; ?> см	<br />
		</p>
		<p>
			Стоимость доставки: <?php echo $this->data->price; ?> р, в том числе НДС <?php echo $this->pit->nds_part; ?><br />
			Время доставки: <?php echo $this->pit->min_delivery_time; ?> - <?php echo $this->pit->max_delivery_time; ?> дн.<br />
			Объем груза: <?php echo $this->pit->volume; ?> m<sup>3</sup><br />
		</p>
		<p>
			Дата выполнения заявки: <?php echo $this->data->produceDate; ?><br />
			Комментарий: <p><?php echo $this->data->comments; ?></p>
		</p>
		<p>
			Данные отправителя:<br />
			<?php if($this->data->sender_legal_type == 'physical'){ ?>
				Тип клиента: Физическое лицо <br />
				ФИО: <?php echo $this->data->sender_name; ?> <br />		
			<?php } else { ?>
				Тип клиента: Юридическое лицо<br />
				Наименование: <?php echo $this->data->sender_company_name; ?> <br />
				ИНН: <?php echo $this->data->sender_inn; ?> <br />
				Юр. адрес: <br />
					Индекс <?php echo $this->data->sender_ZIP_code; ?>	<br />
					г. <?php echo $this->data->sender_juridical_city; ?><br />
					ул. <?php echo $this->data->sender_juridical_street; ?>
					д. <?php echo $this->data->sender_house; ?>
					<?php echo (empty($this->data->sender_building) ? '' : ', корп. '.$this->data->sender_building); ?>
					<?php echo (empty($this->data->sender_structure) ? '' : ', стр. '.$this->data->sender_structure); ?>
					<?php echo (empty($this->data->sender_flat) ? '' : ', кв/оф. '.$this->data->sender_flat); ?><br />
					Контактное лицо: <?php echo $this->data->sender_contact; ?><br />
			<?php } ?>			
			Номер телефона: <?php echo $this->data->sender_phone; ?>
		</p>
		<p>
			Данные получателя:<br />
			<?php if($this->data->receiver_legal_type == 'physical'){ ?>
				Тип клиента: Физическое лицо <br />
				ФИО: <?php echo $this->data->receiver_name; ?> <br />		
			<?php } else { ?>
				Тип клиента: Юридическое лицо<br />
				Наименование: <?php echo $this->data->receiver_company_name; ?> <br />
				ИНН: <?php echo $this->data->receiver_inn; ?> <br />
				Юр. адрес: <br />
					Индекс <?php echo $this->data->receiver_ZIP_code; ?>	<br />
					г. <?php echo $this->data->receiver_juridical_city; ?><br />
					ул. <?php echo $this->data->receiver_juridical_street; ?>
					д. <?php echo $this->data->receiver_house; ?>
					<?php echo (empty($this->data->receiver_building) ? '' : ', корп. '.$this->data->receiver_building); ?>
					<?php echo (empty($this->data->receiver_structure) ? '' : ', стр. '.$this->data->receiver_structure); ?>
					<?php echo (empty($this->data->receiver_flat) ? '' : ', кв/оф. '.$this->data->receiver_flat); ?><br />
					Контактное лицо: <?php echo $this->data->receiver_contact; ?><br />
			<?php } ?>			
			Номер телефона: <?php echo $this->data->receiver_phone; ?>
		</p>
		<p>
			Данные плательщика:<br />
			<?php if ($this->data->payer == 'sender') { echo 'Плательщиком является отправитель'; }
				else if ($this->data->payer == 'receiver') { echo 'Плательщиком является получатель'; }
				else { ?>
				Тип клиента: Юридическое лицо<br />
				Наименование: <?php echo $this->data->third_company_name; ?> <br />
				ИНН: <?php echo $this->data->third_inn; ?> <br />
				Юр. адрес: <br />
					Индекс <?php echo $this->data->third_ZIP_code; ?>	<br />
					г. <?php echo $this->data->third_juridical_city; ?><br />
					ул. <?php echo $this->data->third_juridical_street; ?>
					д. <?php echo $this->data->third_house; ?>
					<?php echo (empty($this->data->third_building) ? '' : ', корп. '.$this->data->third_building); ?>
					<?php echo (empty($this->data->third_structure) ? '' : ', стр. '.$this->data->third_structure); ?>
					<?php echo (empty($this->data->third_flat) ? '' : ', кв/оф. '.$this->data->third_flat); ?><br />
					Контактное лицо: <?php echo $this->data->third_contact; ?><br />	
					Номер телефона: <?php echo $this->data->third_phone; ?>
			<?php }?>
		</p>
	</body>
</html>
