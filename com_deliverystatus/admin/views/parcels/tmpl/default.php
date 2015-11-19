<?php
defined('_JEXEC') or die('Restricted Access');
JHtml::_('script', 'system/core.js', false, true);
JHtml::_('formbehavior.chosen', 'select');

$listOrder = $this->escape($this->filter_order);
$listDirn = $this->escape($this->filter_order_Dir);
?>
<form action="index.php?option=com_deliverystatus&view=parcels" method="post" id="adminForm" name="adminForm">
	<div class="row-fluid">
		<div class="span6">
			<?php echo JText::_('COM_DELIVERYSTATUS_PARCELS_FILTER'); ?>
			<?php echo JLayoutHelper::render('joomla.searchtools.default', array('view' => $this)); ?>
		</div>
	</div>
	<table class="table table-striped table-hover">
		<thead>
		<tr>
			<th width="1%"><?php echo JText::_('COM_DELIVERYSTATUS_NUM'); ?></th>
			<th width="2%">
				<?php echo JHtml::_('grid.checkall'); ?>
			</th>
			<th width="90%">
				<?php echo JHtml::_('grid.sort', 'COM_DELIVERYSTATUS_PARCEL_NUMBER', 'parcel_number', $listDirn, $listOrder); ?>
			</th>
			<th width="5%">
				<?php echo JHtml::_('grid.sort', 'COM_DELIVERYSTATUS_IS_ENABLED', 'is_enabled', $listDirn, $listOrder); ?>
			</th>
			<th width="2%">
				<?php echo JHtml::_('grid.sort', 'COM_DELIVERYSTATUS_ID', 'parcel', $listDirn, $listOrder); ?>
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
				<?php foreach ($this->items as $i => $row) : 
					$link = JRoute::_('index.php?option=com_deliverystatus&task=parcel.edit&parcel=' . $row->parcel);
				?>
 
					<tr>
						<td>
							<?php echo $this->pagination->getRowOffset($i); ?>
						</td>
						<td>
							<?php echo JHtml::_('grid.id', $i, $row->parcel); ?>
						</td>
						<td>
							<a href="<?php echo $link; ?>" title="<?php echo JText::_('COM_DELIVERYSTATUS_EDIT_PARCEL'); ?>">
								<?php echo $row->parcel_number; ?>
							</a>
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
	<input type="hidden" name="task" value=""/>	
	<input type="hidden" name="boxchecked" value="0"/>	
	<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>"/>
	<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>"/>
	<?php echo JHtml::_('form.token'); ?>
</form>
