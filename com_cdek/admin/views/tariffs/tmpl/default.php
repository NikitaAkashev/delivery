<?php
defined('_JEXEC') or die('Restricted Access');
JHtml::_('script', 'system/core.js', false, true);

$listOrder = $this->escape($this->state->get('list.ordering'));
$listDirn  = $this->escape($this->state->get('list.direction'));
?>
<form action="index.php?option=com_cdek&view=tariffs" method="post" id="adminForm" name="adminForm">
	<table class="table table-striped table-hover">
		<thead>
		<tr>
			<th width=""><?php echo JText::_('COM_CDEK_NUM'); ?></th>
			<th width="">
				<?php echo JHtml::_('grid.checkall'); ?>
			</th>
			<th width="">
				<?php echo JHtml::_('grid.sort', 'COM_CDEK_TARIFF_ID', 'tariff_id', $listDirn, $listOrder); ?>
			</th>
			<th width="">
				<?php echo JHtml::_('grid.sort', 'COM_CDEK_TARIFF_NAME', 'tariff_name', $listDirn, $listOrder); ?>
			</th>
			<th width="">
				<?php echo JHtml::_('grid.sort', 'COM_CDEK_PUBLISHED', 'published', $listDirn, $listOrder); ?>
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
					$link = JRoute::_('index.php?option=com_cdek&task=tariff.edit&tariff=' . $row->tariff);
				?>
 
					<tr>
						<td>
							<?php echo $this->pagination->getRowOffset($i); ?>
						</td>
						<td>
							<?php echo JHtml::_('grid.id', $i, $row->tariff); ?>
						</td>
						<td>
							<a href="<?php echo $link; ?>" title="<?php echo JText::_('COM_CDEK_EDIT_TARIFF'); ?>">
								<?php echo $row->tariff_id; ?>
							</a>
						</td>
						<td>
							<a href="<?php echo $link; ?>" title="<?php echo JText::_('COM_CDEK_EDIT_TARIFF'); ?>">
								<?php echo $row->tariff_name; ?>
							</a>
						</td>
						<td align="center">
							<?php echo JHtml::_('jgrid.published', $row->published, $i, 'tariffs.', true, 'cb'); ?>
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
