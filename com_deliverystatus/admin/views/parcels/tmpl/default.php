<?php
defined('_JEXEC') or die('Restricted Access');
JHtml::_('script', 'system/core.js', false, true);
?>
<form action="index.php?option=com_deliverystatus&view=parcels" method="post" id="adminForm" name="adminForm">
	<table class="table table-striped table-hover">
		<thead>
		<tr>
			<th width="1%"><?php echo JText::_('COM_DELIVERYSTATUS_NUM'); ?></th>
			<th width="2%">
				<?php echo JHtml::_('grid.checkall'); ?>
			</th>
			<th width="90%">
				<?php echo JText::_('COM_DELIVERYSTATUS_PARCEL_NUMBER') ;?>
			</th>
			<th width="5%">
				<?php echo JText::_('COM_DELIVERYSTATUS_IS_ENABLED'); ?>
			</th>
			<th width="2%">
				<?php echo JText::_('COM_DELIVERYSTATUS_ID'); ?>
			</th>
		</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="5">
					<?php echo $this->pagination->getListFooter(); ?>
				</td>
			</tr>
		</tfoot>
		<tbody>
			<?php if (!empty($this->items)) : ?>
				<?php foreach ($this->items as $i => $row) : ?>
 
					<tr>
						<td>
							<?php echo $this->pagination->getRowOffset($i); ?>
						</td>
						<td>
							<?php echo JHtml::_('grid.id', $i, $row->parcel); ?>
						</td>
						<td>
							<?php echo $row->parcel_number; ?>
						</td>
						<td align="center">
							<?php echo JHtml::_('jgrid.published', $row->is_enabled, $i, 'parcels.', true, 'cb'); ?>
						</td>
						<td align="center">
							<?php echo $row->parcel; ?>
						</td>
					</tr>
				<?php endforeach; ?>
			<?php endif; ?>
		</tbody>
	</table>
</form>
