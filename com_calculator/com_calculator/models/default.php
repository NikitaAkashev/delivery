<?php // no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );
 
class CalculatorModelsDefault extends JModelBase
{
 
	function __construct()
	{
		parent::__construct();
	}
 
	public function store($data)
	{
		$row = JTable::getInstance($data['table'],'Table');
	 
		$date = date("Y-m-d H:i:s");
	 
		// Bind the form fields to the table
		if (!$row->bind($data))
		{
			return false;
		}
		
		// надо ли...
		$row->modified = $date;
		if ( !$row->created )
		{
			$row->created = $date;
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
?>
