<?php
defined('_JEXEC') or die('Restricted access');
JHtml::_('behavior.formvalidation');
?>
<form action="<?php echo JRoute::_('index.php?option=com_cdek&layout=edit&tariff=' . (int) $this->item->tariff); ?>"
    method="post" name="adminForm" id="adminForm" class="form-validate">
    <div class="form-horizontal">
        <fieldset class="adminform">
            <div class="row-fluid">
                <div class="span6">
            		<legend><?php echo JText::_('COM_CDEK_TARIFF_DETAILS'); ?></legend>
                    <?php foreach ($this->form->getFieldset() as $field): ?>
                        <div class="control-group">
                            <div class="control-label"><?php echo $field->label; ?></div>
                            <div class="controls"><?php echo $field->input; ?></div>
                        </div>
                    <?php endforeach; ?>
                </div>
        </fieldset>
    </div>
    <input type="hidden" name="task" value="tariff.edit" />
    <?php echo JHtml::_('form.token'); ?>
</form>