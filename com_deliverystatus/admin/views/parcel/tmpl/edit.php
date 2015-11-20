<?php
defined('_JEXEC') or die('Restricted access');
JHtml::_('behavior.formvalidation');
?>
<form action="<?php echo JRoute::_('index.php?option=com_deliverystatus&layout=edit&parcel=' . (int) $this->item->parcel); ?>"
    method="post" name="adminForm" id="adminForm" class="form-validate">
    <div class="form-horizontal">
        <fieldset class="adminform">
            <div class="row-fluid">
                <div class="span6">
            		<legend><?php echo JText::_('COM_DELIVERYSTATUS_PARCEL_DETAILS'); ?></legend>
                    <?php foreach ($this->form->getFieldset() as $field): ?>
                        <div class="control-group">
                            <div class="control-label"><?php echo $field->label; ?></div>
                            <div class="controls"><?php echo $field->input; ?></div>
                        </div>
                    <?php endforeach; ?>
                </div>
                <div class="span6">
               		<legend><?php echo JText::_('COM_DELIVERYSTATUS_PARCEL_STATUS_ADD'); ?></legend>
                    <?php foreach ($this->status_form->getFieldset() as $field): ?>
                        <div class="control-group">
                            <div class="control-label"><?php echo $field->label; ?></div>
                            <div class="controls"><?php echo $field->input; ?></div>
                        </div>
                    <?php endforeach; ?>
                    <legend><?php echo JText::_('COM_DELIVERYSTATUS_PARCEL_STATUS_LIST'); ?></legend>
                    <?php foreach ($this->statuses as $status): ?>
                        <div class="control-group">
                            <div class="control-label"><?php echo $status->dt; ?></div>
                            <div class="controls"><?php echo $status->name; ?></div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </fieldset>
    </div>
    <input type="hidden" name="task" value="parcel.edit" />
    <?php echo JHtml::_('form.token'); ?>
</form>