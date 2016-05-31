<?php // no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );
 
class CdekModelsDefault extends JModelBase
{
 
	function __construct()
	{
		parent::__construct();
	}
 
	public function store($data)
	{
		$row = JTable::getInstance($data['table'],'Table');
			 
		// Bind the form fields to the table
		if (!$row->bind($data))
		{
			return false;
		}
	 
		// Make sure the record is valid
		if (!$row->check())
		{
			return false;
		}
		// Store the web link table to the database
		if (!$row->store())
		{
			return false;
		}
	 
		return $row;
	}
}
?>
