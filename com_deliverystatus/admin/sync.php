<?php
class Syncer
{
	private $_cdek_login;
	private $_cdek_password;
	private $_cdek_url;
	private $_cdek_days;
	
	private $_mysqli;
	private $_sql_prefix;
	
	function __construct($cdek_login, $cdek_password, $cdek_url, $cdek_days, 
		$sql_host, $sql_user, $sql_password, $sql_db, $sql_prefix)
	{
		$this->_cdek_login = $cdek_login;
		$this->_cdek_password = $cdek_password;
		$this->_cdek_url = $cdek_url;
		$this->_cdek_days = $cdek_days;
		
		$this->_mysqli = new mysqli($sql_host, $sql_user, $sql_password, $sql_db);
		if ($this->_mysqli->connect_error) {
			die('Connect Error (' . $this->_mysqli->connect_errno . ') ' . $this->_mysqli->connect_error);
		}
		
		$this->_sql_prefix = $sql_prefix;
	} 
	
	function __destruct()
	{
		$this->_mysqli->close();
	}
	
	private function RealQuery($query)
	{
		return str_replace("#__", $this->_sql_prefix, $query);
	}
	
	private function GetOrdersRemote()
	{
		$date = date("Y-m-d", time() - 60 * 60 * 24 * $this->_cdek_days);

		$secure = md5($date.'&'.$this->_cdek_password);

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $this->_cdek_url.'?account='.$this->_cdek_login.'&secure='.$secure.'&datefirst='.$date);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER,true);
		
		$out = curl_exec($ch);
		
		curl_close($ch);
			
		$order = simplexml_load_string ($out);
		
		$orders = array();
		
		if (!count($order->Order))
			return;
		
		foreach($order->Order as $ord)
		{
			$row = array();
			$attrs = $ord->attributes();
			$status_attrs = $ord->Status->attributes();
			
			$row['outer_id'] = (string)$attrs['DispatchNumber'];	
			$row['dt'] = (string)$status_attrs['Date'];
			$row['code'] = (string)$status_attrs['Code'];
			
			$orders[] = $row;
		}
		
		return $orders;
	}

	private function GetOrdersLocal()
	{
		$query = <<<TXT
select 
	p.parcel, 
	p.outer_id,
	s.code,
	ps.dt
from #__delivery_parcel p
left join (
		select ps.parcel, max(ps.dt) dt
		from #__delivery_parcel p
			join #__delivery_parcel2parcel_status ps on ps.parcel = p.parcel
		group by ps.parcel
	) pdt on p.parcel = pdt.parcel
left join #__delivery_parcel2parcel_status ps on ps.parcel = p.parcel and ps.dt = pdt.dt
left join #__delivery_parcel_status s on s.parcel_status = ps.parcel_status
where
	p.outer_id <> ''
	and p.outer_id is not null
	and (s.code <> '4' -- статус не "Вручен"
		or s.code is null)
TXT;
		
		$query = $this->RealQuery($query);
		
		$result = $this->_mysqli->query($query) or die($this->_mysqli->error);
		
		$orders = array();
		while ($row = $result->fetch_assoc()) {
			$orders[] = $row;
		}
		
		return $orders;
	}

	private function SetStatus($data)
	{
		$template = <<<TXT
insert into #__delivery_parcel2parcel_status(parcel, parcel_status, dt)
select '%s', parcel_status, '%s' from #__delivery_parcel_status where code = '%s';

TXT;
		$query = '';
		
		foreach($data as $r)
		{
			$query .= sprintf($template, 
				$this->_mysqli->real_escape_string($r['parcel']), 
				$this->_mysqli->real_escape_string($r['dt']), 
				$this->_mysqli->real_escape_string($r['code']));
		}
		$query = $this->RealQuery($query);
		
		if($this->_mysqli->multi_query($query) !== TRUE)
			echo $this->_mysqli->error;
	}

	function Sync()
	{
		$remote = $this->GetOrdersRemote();
		if (!count($remote))
			return;
		
		$local = $this->GetOrdersLocal();
		if (!count($local))
			return;
			
		$new_data = array();
		foreach($local as $loc)
		{
			foreach($remote as $rem)
			{
				if($rem['outer_id'] == $loc['outer_id'] && $rem['code'] != $loc['code'])
				{
					$new_data[] = array('parcel' => $loc['parcel'], 'code' => $rem['code'], 'dt' => $rem['dt']);
				}
			}
		}
		
		if (!count($new_data))
			return;
		
		$this->SetStatus($new_data);		
	}
}

$worker = new Syncer("cdeklogin", "cdekpass", "cdekurl", 3,
	'sqlhost', 'sqluser', 'sqlpass', 'sqldb', 'sqlprefix');

$worker->Sync();

?>
