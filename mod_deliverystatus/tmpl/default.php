<?php
// No direct access
defined( '_JEXEC' ) or die;
	
    $items = JFactory::getApplication()->getMenu( 'site' )->getItems( 'component', 'com_deliverystatus' );
    foreach ( $items as $item ) {
        if($item->query['view'] === 'deliverystatus'){
            $itemId = $item->id;
        }
    }
?>
 
<div class="module">
	<p><?php echo JText::_('MOD_DELIVERYSTATUS_DESC'); ?></p>
	<form method="get" action="<?php echo JRoute::_('index.php?option=com_deliverystatus&Itemid='.$itemId); ?>">
		
		<input name="parcel_number" class="replace" type="text" placeholder="<?php echo JText::_('MOD_DELIVERYSTATUS_PARCEL_NUMBER'); ?>" value="" />
		
		<input type="submit" value="<?php echo JText::_('MOD_DELIVERYSTATUS_SUBMIT_BUTTON'); ?>"/>
	</form>
</div>
