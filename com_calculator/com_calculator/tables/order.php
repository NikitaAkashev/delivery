<?php defined( '_JEXEC' ) or die( 'Restricted access' );
	class OrderBook extends JTable
	{
		/**
		* Constructor
		*
		* @param object Database connector object
		*/
		function __construct( &$db )
		{
			parent::__construct('#__calc_order', 'order', $db);
		}
	}
?>
