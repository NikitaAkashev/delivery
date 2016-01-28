<?php
// No direct access
defined( '_JEXEC' ) or die;
?>
<div class="module">
	<form method="get" action="<?php JUri::base() ?>/index.php">
		<input name="option" value="com_deliverystatus" type="hidden" />
		
		<input name="parcel_number" class="replace" type="text" placeholder="<?php echo JText::_('MOD_DELIVERYSTATUS_SUBMIT_BUTTON'); ?>" value="" />
		
		<input type="submit"/>
	</form>
</div>