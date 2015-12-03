<?php
// No direct access
defined( '_JEXEC' ) or die;
?>
<div class="module">
	<form method="get" action="/index.php">
		<input name="option" value="com_deliverystatus" type="hidden" />
		
		<input name="parcel_number" class="replace" type="text" value="<?php echo JText::_('MOD_DELIVERYSTATUS_SUBMIT_BUTTON'); ?>" />
		
		<input type="submit"/>
	</form>
</div>