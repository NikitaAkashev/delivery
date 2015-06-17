<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
// import Joomla modelitem library
jimport('joomla.application.component.modelitem');
 
/**
 *  Model
 */
class CalculatorModelsOrder extends CalculatorModelsDefault
{
	//private $_volume_weight_divider = 6000;
	//private $_dimension_limit = 300;
	//private $_weight_limit = 200;
	private $_inner_price_viewer_group_ids = array(7,8); // ID групп, которым можно считать разницу в ценах.
	
	private $user_id;
	
	public $nds = 0.18;
	
	//public $is_express;
	//public $from_door;
	public $city_from;
	public $city_to;
	public $weight;
	public $assessed_value;
	public $width;
	public $length;
	public $height;
	
	public $prices;
	
	public $volume;
	
	public $ordered = false;
	
	// и вдруг, не мудурствуя лукаво, берем и фигачим весь реквест в переменную!!!
	public $form;
	
	function __construct() {
		parent::__construct();
		
		$this->user_id = JFactory::getUser()->get('id');
				
		$this->city_from = JRequest::getInt('city_from', null);
		$this->city_to = JRequest::getInt('city_to', null);				
		$this->weight = JRequest::getFloat('weight', null);    
		$this->assessed_value = JRequest::getFloat('assessed_value', null);    
		$this->width = JRequest::getFloat('width', null);    
		$this->length = JRequest::getFloat('length', null);    
		$this->height = JRequest::getFloat('height', null);
		    
		$this->form = JRequest::get();
	}
	
	// проверяет, что переданы все необходимые данные для расчета
	function IsFilled(){
		return isset($this->city_from) && isset($this->city_to) && isset($this->weight) &&
				isset($this->assessed_value) && isset($this->width) &&
				isset($this->length) && isset($this->height) && 
				$this->city_from != 0 && $this->city_to != 0 && 
				$this->weight != 0 && $this->width != 0 &&
				$this->length != 0 && $this->height != 0;
	}
	
	// проверяет, можно ли пользователю смотреть внутреннюю стоимость отправки
	function IsInnerPriceViewer(){
		$usergroups = JAccess::getGroupsByUser($this->user_id);
		foreach ($this->_inner_price_viewer_group_ids as $agid)
		{
			if (in_array($agid,$usergroups)) return true;
		}	  
		return false;
	}
	
	// Производит расчет
	function Calculate(){
		if($this->IsFilled()){

			$oversize = $this->width > $this->_dimension_limit || 
						$this->length > $this->_dimension_limit || 
						$this->height > $this->_dimension_limit ||
						$real_weight > $this->_weight_limit ? 1.5 : 1;
			
			$db = JFactory::getDBO();
			$query = "
/* болванка */
select 1 as foo
	
";
			$db->setQuery($query);
			$result = $db->loadObject();
						
			if(is_null($result))
			{
				$this->prices = null;
				return;
			}
									
			$discount = (100 - $result->discount)/100;
			
			$weight_price = $result->weight_base + $result->weight_over * (ceil($real_weight) - $result->weight_bottom);
			$assessed_value_price = $result->assessed_value_base + $result->assessed_value_over * (ceil($this->assessed_value) - $result->assessed_value_bottom);
			
			if($is_public){
				$this->price = $weight_price * $oversize * ($result->factor_from + $result->factor_to - 1)* $discount + $assessed_value_price;
				
				if($this->city_from == 38){// Москва
					$this->min_delivery_time = $result->t_min_time;
					$this->max_delivery_time = $result->t_max_time;	
				} else if($this->city_to == 38){// Москва
					$this->min_delivery_time = $result->f_min_time;
					$this->max_delivery_time = $result->f_max_time;	
				} else if ($result->f_min_time == 1){
					$this->min_delivery_time = $result->t_min_time + 1;
					$this->max_delivery_time = $result->t_max_time + 1;					
				} else if ($result->t_min_time == 1){
					$this->min_delivery_time = $result->f_min_time + 1;
					$this->max_delivery_time = $result->f_max_time + 1;	
				} else {
					$this->min_delivery_time = $result->f_min_time + $result->t_min_time;
					$this->max_delivery_time = $result->f_max_time + $result->t_max_time;
				}
				
				$this->nds_part = ceil($this->nds * $this->price / (1 + $this->nds) * 100 ) / 100;
				$this->volume = $this->width * $this->length * $this->height / 1000000;
			} else
			{
				$this->inner_price = $weight_price * $oversize * ($result->factor_from + $result->factor_to - 1) * $discount + $assessed_value_price;
				$this->nds_part_inner = ceil($this->nds * $this->inner_price / (1 + $this->nds) * 100) / 100;
				$this->profit = $this->price - $this->inner_price;
				$this->profit_nds_part = ceil($this->nds * $this->profit / (1 + $this->nds) * 100) / 100;
			}
		} else {
			$this->price = null;
		}
	}

	// проверим, что пришли все данные, которые нам нужны для заказа TODO: Перенести проверку в JTable::check();
	function CheckOrderData()
	{		
		// установлена дата заказа	
		if (empty($this->form['comments']))
			return false;
		
		return true;
	}
	
	// Отправим заказ
	function MakeOrder()
	{
		if($this->CheckOrderData())
		{	
			// сохраним в лог
			$row = $this->LogOrder($this->form);
			
			$view = CalculatorHelpersView::load('email', 'normal', 'html', array('data' => $row, 'pit' => $this)); // TODO когда будет тариф, pit станет не нужен
			
			// Render our view.
			$message = $view->render();
			
			// отправим мыло			
			$to      = 'regspambox@yandex.ru';
			$subject = 'Заказ с сайта ... дописать';
			$headers = 'MIME-Version: 1.0' . "\r\n".
						'Content-type: text/html; charset=utf-8' . "\r\n" .
						'From: webmaster@example.com' . "\r\n" .
						'Reply-To: webmaster@example.com' . "\r\n" .
						'X-Mailer: PHP/' . phpversion();

			mail($to, $subject, $message, $headers);
			
			$this->ordered = true;
			$this->order_message = $message;
		}
	}	
		
	// Логирование заказа
	function LogOrder($data){
		$date = date("Y-m-d H:i:s");
		
		$data['table'] = 'order';
		$data['created'] = $date;
		$data['modified'] = $date;
		$data['price'] = $this->price;
		$data['user'] = $this->user_id;
		$data['order_status'] = 1; // magic number, TODO переделать на получение по коду
		$data['tariff'] = 0; // TODO После переделки системы тарифов сделать сюда вставку идентификатора тарифа
		
		return $this->store($data);
	}
}
?>
