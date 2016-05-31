<?php
defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.modelitem');

class CdekModelsOrder extends CdekModelsDefault
{
	private $user_id;
	
	public $nds = 0.18;
	
	public $city_from;
	public $city_to;
	public $weight;
	
	public $width;
	public $length;
	public $height;	
	
	public $volume;
	
	public $prices = array();
	
	public $calculated = false;
	public $ordered = false;
	
	// и вдруг, не мудурствуя лукаво, берем и фигачим весь реквест в переменную!!!
	public $form;
	
	function __construct() {
		parent::__construct();
		
		$this->user_id = JFactory::getUser()->get('id');
				
		$this->city_from = JRequest::getInt('city_from', null);
		$this->city_to = JRequest::getInt('city_to', null);				
		$this->weight = JRequest::getFloat('weight', null);
		  
		$this->width = JRequest::getFloat('width', null);    
		$this->length = JRequest::getFloat('length', null);    
		$this->height = JRequest::getFloat('height', null);
		    
		$this->form = JRequest::get();
	}
	
	// проверяет, что переданы все необходимые данные для расчета
	function IsFilled(){
		return 
			isset($this->city_from) 
			&& isset($this->city_to) 
			&& $this->city_from != 0 
			&& $this->city_to != 0 
			&& isset($this->weight) 
			&& $this->weight != 0 
			&& isset($this->assessed_value) 
			&&(
				$this->weight <= $this->weight_no_size || // либо вес меньше граничного, либо размеры заполнены
				(
					isset($this->width) 
					&& isset($this->length) 
					&& isset($this->height) 
					&& $this->width != 0 
					&& $this->length != 0 
					&& $this->height != 0
				)
			);
	}
	
	// Производит расчет
	function Calculate(){
		if($this->IsFilled())
		{
			
		} 
		else 
		{
			$this->CalculationSetEmpty();			
		}
		return $this->CalculationResult();
	}

	// проверка корректности заполнения телефонного номера
	static function IsPhoneValid($phone)
	{
		if (!preg_match("/^[\d \+\-\(\)]+$/", $phone))
			return false;
		
		return true;
	}

	// проверим, что пришли все данные, которые нам нужны для заказа TODO: Перенести проверку в JTable::check();
	function CheckOrderData()
	{		
		if (empty($this->form['make_order']) || $this->form['make_order'] != 'sure')
			return false;
		
		if (!CalculatorModelsOrder::IsPhoneValid($this->form['phone']))
			return false;
		
		return true;
	}
	
	// возвращает объект с результатами расчетов
	function CalculationResult()
	{
		$result = new stdClass();
		$result->calculated = $this->calculated;
		$result->with_inner = $this->with_inner;
		$result->prices = $this->prices;
		return $result;
	}
	
	// Устанавливает пустые значения, если расчет не выполнился
	function CalculationSetEmpty()
	{
		$this->calculated = false;
		$this->with_inner = $this->with_inner;
		$this->prices = array();
	}
	
	// Отправим заказ
	function MakeOrder()
	{
		if($this->CheckOrderData())
		{	
			// сохраним в лог
			$row = $this->LogOrder($this->form);
			
			$view = CalculatorHelpersView::load('email', 'normal', 'html', array('data' => $row));
			
			// Render our view.
			$message = $view->render();
			
			$requesites = $this->GetEmailRequisites();
					
			// отправим мыло			
			$headers = 'MIME-Version: 1.0' . "\r\n".
						'Content-type: text/html; charset=utf-8' . "\r\n" .
						'From: '. $requesites->from . "\r\n" .
						'Reply-To: '. $requesites->to . "\r\n" .
						'X-Mailer: PHP/' . phpversion() . "\r\n" .
						(!empty($this->form['email']) && filter_var($this->form['email'], FILTER_VALIDATE_EMAIL) ? 'BCC: ' . $this->form['email'] . "\r\n" : ''); // Если есть мыло клиента, то пошлем копию ему

			mail($requesites->to, $requesites->subject, $message, $headers);
			
			$this->ordered = true;
			$this->order_message = $message;
		}
	}	
		
	// Логирование заказа
	function LogOrder($data){
		$date = date("Y-m-d H:i:s");
		
		$order_unique = explode('_', $data['calc_row_id'], 2); // Уникальные характеристики заказа.
				
		$data['table'] = 'order';
		$data['created'] = $date;
		$data['modified'] = $date;
		$data['rate'] = $order_unique[0];
		$data['delivery_type_code'] = $order_unique[1];
		$data['user'] = $this->user_id;
		
		return $this->store($data);
	}
	
	
	// Получение реквизитов для отправки письма
	function GetEmailRequisites()
	{		
		$db = JFactory::getDBO();
		$query = "
select 
	s.value `to`,
	ss.value `subject`,
	mf.value `from`
from #__delivery_settings s
	join #__delivery_settings ss on ss.code = 'mail_subject'
	join #__delivery_settings mf on mf.code = 'mail_from'
where s.code = 'mail_to'
";
		$db->setQuery($query);
		$result = $db->loadObject();
		
		return $result;
	}
}
?>
