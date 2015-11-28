<?php

defined('_JEXEC') or die('Restricted access');
?>

<?php if (array_key_exists('parcel_number', $this->parcel) && $this->parcel['parcel_number']) { ?>
	Номер накладной: <?php echo $this->parcel['parcel_number'] ?> <br />
<?php } ?>

<?php if (array_key_exists('created', $this->parcel) && $this->parcel['created']) { ?>
	Дата создания накладной: <?php $date = new DateTime($this->parcel['created']); echo $date->format('d.m.Y'); ?> <br />
<?php } ?>

<?php if (array_key_exists('sender', $this->parcel) && $this->parcel['sender']) { ?>
	Отправитель: <?php echo $this->parcel['sender'] ?> <br />
<?php } ?>

<?php if (array_key_exists('receiver', $this->parcel) && $this->parcel['receiver']) { ?>
	Получатель: <?php echo $this->parcel['receiver'] ?> <br />
<?php } ?>

<?php if (array_key_exists('payer', $this->parcel) && $this->parcel['payer']) { ?>
	Плательщик: <?php echo $this->parcel['payer'] ?> <br />
<?php } ?>

<?php if (array_key_exists('address_from', $this->parcel) && $this->parcel['address_from']) { ?>
	Откуда: <?php echo $this->parcel['address_from'] ?> <br />
<?php } ?>

<?php if (array_key_exists('address_to', $this->parcel) && $this->parcel['address_to']) { ?>
	Куда: <?php echo $this->parcel['address_to'] ?> <br />
<?php } ?>

<?php if (array_key_exists('mem', $this->parcel) && $this->parcel['mem']) { ?>
	Дополнительная информация: 
	<p><?php echo $this->parcel['mem'] ?></p>
<?php } ?>

<?php if (count($this->statuses)) { ?>
	Статус доставки: <br />
	<?php foreach($this->statuses as $status) :?>
		<?php echo $status->dt . ' ' . $status->name; ?><br />
	<?php endforeach;?>
<?php } ?>
