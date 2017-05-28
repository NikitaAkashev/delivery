<?php
class Syncer
{	
	private $_mysqli;
	
	private $_sql_prefix_from;
	private $_sql_db_from;
	private $_sql_prefix_to;
	private $_sql_db_to;
	
	function __construct($sql_host, $sql_user, $sql_password, 
							$sql_db_from, $sql_prefix_from,
							$sql_db_to, $sql_prefix_to)
	{		
		$this->_mysqli = new mysqli($sql_host, $sql_user, $sql_password, $sql_db_to);
		if ($this->_mysqli->connect_error) {
			die('Connect Error (' . $this->_mysqli->connect_errno . ') ' . $this->_mysqli->connect_error);
		}
		
		$this->_sql_prefix_from = $sql_prefix_from;
		$this->_sql_db_from = $sql_db_from;
		$this->_sql_prefix_to = $sql_prefix_to;
		$this->_sql_db_to = $sql_db_to;
	} 
	
	function __destruct()
	{
		$this->_mysqli->close();
	}
	
	
	private function RealQuery($query)
	{
		$query = str_replace("#pf", $this->_sql_prefix_from, $query);
		$query = str_replace("#pt", $this->_sql_prefix_to, $query);
		$query = str_replace("#df", $this->_sql_db_from, $query);
		$query = str_replace("#dt", $this->_sql_db_to, $query);
		
		return $query;
	}
	
	function Sync()
	{
		$query = <<<TXT
update #df.#pf_delivery_parcel sp, #dt.#pt_delivery_parcel ep set
	ep.published = sp.published,
    ep.owner = case when sp.owner = 19 then 453 else sp.owner end,
    ep.creator = case when sp.creator = 19 then 453 else sp.creator end,
    ep.parcel_number = sp.parcel_number,
    ep.created = sp.created,
    ep.sender = sp.sender,
    ep.receiver = sp.receiver,
    ep.payer = sp.payer,
    ep.address_from = sp.address_from,
    ep.address_to = sp.address_to,
    ep.mem = sp.mem,
    ep.places_amount = sp.places_amount,
    ep.weight = sp.weight,
    ep.volume = sp.volume
where
	ep.parcel_number=sp.parcel_number;


insert into #dt.#pt_delivery_parcel(published,
    owner,
    creator,
    parcel_number,
    created,
    sender,
    receiver,
    payer,
    address_from,
    address_to,
    mem,
    places_amount,
    weight,
    volume)
select 
	published,
    case when owner = 19 then 453 else owner end owner,
    case when creator = 19 then 453 else creator end creator,
    parcel_number,
    created,
    sender,
    receiver,
    payer,
    address_from,
    address_to,
    mem,
    places_amount,
    weight,
    volume
 from #df.#pf_delivery_parcel sp
where not exists (select 1 from #dt.#pt_delivery_parcel ep where ep.parcel_number=sp.parcel_number);


insert into #dt.#pt_delivery_parcel2parcel_status(
	parcel,
    parcel_status,
    dt
)
select 
	p.parcel,
    sps.parcel_status,
    sps.dt
from #df.#pf_delivery_parcel2parcel_status sps
	join #df.#pf_delivery_parcel sp on sp.parcel = sps.parcel
    join #dt.#pt_delivery_parcel p on p.parcel_number = sp.parcel_number
	where not exists (select 1 from #dt.#pt_delivery_parcel2parcel_status eps
						join #dt.#pt_delivery_parcel ep on ep.parcel = eps.parcel
                      where
						sp.parcel_number = ep.parcel_number
						and sps.parcel_status = eps.parcel_status
                        );
TXT;
		$query = $this->RealQuery($query);
		
		if($this->_mysqli->multi_query($query) !== TRUE)
			die($this->_mysqli->error);
		
		return;
	}
}

// префиксы без последнего "_"
$worker = new Syncer('dbhost', 'dbuser', 'dbpass', 
						'db_from', 'prefix_from',
						'db_to', 'prefix_to');

$worker->Sync();

?>
