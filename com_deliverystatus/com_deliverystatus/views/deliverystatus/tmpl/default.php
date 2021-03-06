<?php

defined('_JEXEC') or die('Restricted access');
?>

<div id="deliverystatus">
<?php if (array_key_exists('parcel_number', $this->parcel) && $this->parcel['parcel_number']) { ?>
	<b>Номер накладной:</b> <?php echo $this->parcel['parcel_number'] ?> <br />
<?php } ?>

<?php if (array_key_exists('created', $this->parcel) && $this->parcel['created']) { ?>
	<b>Дата создания накладной:</b> <?php $date = new DateTime($this->parcel['created']); echo $date->format('d.m.Y'); ?> <br />
<?php } ?>

<?php if (array_key_exists('sender', $this->parcel) && $this->parcel['sender']) { ?>
	<b>Отправитель:</b> <?php echo $this->parcel['sender'] ?> <br />
<?php } ?>

<?php if (array_key_exists('receiver', $this->parcel) && $this->parcel['receiver']) { ?>
	<b>Получатель:</b> <?php echo $this->parcel['receiver'] ?> <br />
<?php } ?>

<?php if (array_key_exists('payer', $this->parcel) && $this->parcel['payer']) { ?>
	<b>Плательщик:</b> <?php echo $this->parcel['payer'] ?> <br />
<?php } ?>

<?php if (array_key_exists('address_from', $this->parcel) && $this->parcel['address_from']) { ?>
	<b>Откуда:</b> <?php echo $this->parcel['address_from'] ?> <br />
<?php } ?>

<?php if (array_key_exists('address_to', $this->parcel) && $this->parcel['address_to']) { ?>
	<b>Куда:</b> <?php echo $this->parcel['address_to'] ?> <br />
<?php } ?>

<?php if (array_key_exists('mem', $this->parcel) && $this->parcel['mem']) { ?>
	<b>Дополнительная информация:</b> 
	<p><?php echo $this->parcel['mem'] ?></p>
<?php } ?>

<?php if (array_key_exists('places_amount', $this->parcel) && $this->parcel['places_amount']) { ?>
	<b>Количество мест:</b> <?php echo $this->parcel['places_amount'] ?> <br />
<?php } ?>

<?php if (array_key_exists('weight', $this->parcel) && $this->parcel['weight']) { ?>
	<b>Вес:</b> <?php echo $this->parcel['weight'] ?> кг<br />
<?php } ?>

<?php if (array_key_exists('volume', $this->parcel) && $this->parcel['volume']) { ?>
	<b>Объем:</b> <?php echo $this->parcel['volume'] ?> м<sup>3</sup><br />
<?php } ?>

<?php if (count($this->statuses)) { ?>
	<b>Статус доставки:</b> <br />
	<?php foreach($this->statuses as $status) :?>
		<?php echo date("d.m.Y", strtotime($status->dt)) . ' ' . $status->name; ?><br />
	<?php endforeach;?>
<?php } ?>

<?php
	$isModal = JRequest::getVar( 'print' ) == 1; // 'print=1' will only be present in the url of the modal window, not in the presentation of the page
	if( $isModal) {
		$href = '"#" onclick="window.print(); return false;"';
	} else {
		$href = 'status=no,toolbar=no,scrollbars=yes,titlebar=no,menubar=no,resizable=yes,width=640,height=480,directories=no,location=no';
		$href = "onclick=\"window.open(this.href,'win2','".$href."'); return false;\"";
		$href = '"'.JRoute::_('index.php?option=com_deliverystatus&Itemid='.$this->item_id.'&parcel_number='.$this->parcel_number.'&tmpl=component&print=1').'" '.$href;
	}
?>
 	<a class="print" href=<?php echo $href; ?> >Печать</a>
</div>