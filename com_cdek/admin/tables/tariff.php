<?php
defined('_JEXEC') or die('Restricted access');

class CdekTableTariff extends JTable
{
	function __construct(&$db)
	{
		parent::__construct('#__cdek_tariff', 'tariff', $db);
	}
}
?>