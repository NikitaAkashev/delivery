<?php
defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.modelitem');
include_once ("CalculatePriceDeliveryCdek.php");

class CdekModelsOrder extends CdekModelsDefault
{
	public $city_from;
	public $city_to;
	public $weight;
	
	public $width;
	public $length;
	public $height;	
	
	public $volume;

	public $form;

	// Список полученных цен
	public $prices = array();
	
	public $calculated = false;
	public $ordered = false;

	public $settings;
	public $user_settings;

	function __construct() {
		parent::__construct();
				
		$this->city_from = JRequest::getInt('city_from_id', null);
		$this->city_to = JRequest::getInt('city_to_id', null);
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
			&& ( $this->weight <= $this->GetSettings()->weight_no_size
				||
				(isset($this->width)
				&& isset($this->length)
				&& isset($this->height)
				&& $this->width != 0
				&& $this->length != 0
				&& $this->height != 0)
			);
	}
	
	// Производит расчет
	function Calculate(){
		if($this->IsFilled())
		{
			$settings = $this->GetSettings();
			$user_settings = $this->GetUserSettings();
			$interest = $user_settings->interest ? $user_settings->interest : $settings->interest;
			$with_nds = $user_settings->with_nds;
			$ceil_to = $settings->ceil_to;
			$result = array();
			$tariffs = $this->GetTariffs();
			foreach ($tariffs as $tariff)
			{
				$res = $this->CalculateByTariff($tariff->tariff_id);
				if($res)
				{
					// получим нашу цену
					$price = $res['price'] * $interest;

					// если указано, до куда округлять, округлим
					if($ceil_to)
						$price = ceil($price/$ceil_to) * $ceil_to;
					
					// добавим НДС, если надо
					$nds = 0;
					if($with_nds)
						$nds = ceil($price * 0.18);
					
					$price = $price + $nds;
					
					$res['price'] = $price;
					$res['nds'] = $nds;

					$res['name'] = $tariff->tariff_name;
					$res['delivery_time'] = $res['deliveryPeriodMin'] == $res['deliveryPeriodMax'] ? $res['deliveryPeriodMax'] : $res['deliveryPeriodMin'] . ' - ' . $res['deliveryPeriodMax'];
					$result[] = $res;
				}
			}

			$this->prices = $result;

			$this->calculated = true;
		} 
		else 
		{
			$this->CalculationSetEmpty();			
		}
		return $this->CalculationResult();
	}


	// расчет стоимости отправки по тарифу
	function CalculateByTariff($tariff)
	{
		//создаём экземпляр объекта CalculatePriceDeliveryCdek
		$calc = new CalculatePriceDeliveryCdek();

		//Авторизация. Для получения логина/пароля (в т.ч. тестового) обратитесь к разработчикам СДЭК -->
		//$calc->setAuth('authLoginString', 'passwordString');

		//устанавливаем город-отправитель
		$calc->setSenderCityId($this->city_from);
		//устанавливаем город-получатель
		$calc->setReceiverCityId($this->city_to);
		//устанавливаем дату планируемой отправки
		//$calc->setDateExecute(new DateTime("now"));

		//устанавливаем тариф по-умолчанию
		$calc->setTariffId($tariff);

		//добавляем места в отправление
		$calc->addGoodsItemBySize($this->weight, $this->length ? $this->length : 1, $this->width ? $this->width : 1, $this->height ? $this->height : 1);
		//$calc->addGoodsItemByVolume($_REQUEST['weight2'], $_REQUEST['volume2']);

		if ($calc->calculate() === true) {
			$res = $calc->getResult();

			return $res['result'];
		}

		return null;
	}

	// проверка корректности заполнения телефонного номера
	static function IsPhoneValid($phone)
	{
		if (!preg_match("/^[\d \+\-\(\)]+$/", $phone))
			return false;
		
		return true;
	}

	// проверим, что пришли все данные, которые нам нужны для заказа
	function CheckOrderData()
	{		
		if (empty($this->form['make_order']) || $this->form['make_order'] != 'sure')
			return false;
		
		if (!CdekModelsOrder::IsPhoneValid($this->form['phone']))
			return false;
		
		return true;
	}
	
	// возвращает объект с результатами расчетов
	function CalculationResult()
	{
		$result = new stdClass();
		$result->calculated = $this->calculated;
		$result->prices = $this->prices;
		return $result;
	}
	
	// Устанавливает пустые значения, если расчет не выполнился
	function CalculationSetEmpty()
	{
		$this->calculated = false;
		$this->prices = array();
	}
	
	// Отправим заказ
	function MakeOrder()
	{
		if($this->CheckOrderData())
		{	
			// сохраним в лог
			$row = $this->LogOrder($this->form);
			
			$view = CdekHelpersView::load('email', 'normal', 'html', array('data' => $row));
			
			// Render our view.
			$message = $view->render();
			
			$requesites = $this->GetSettings();
					
			// отправим мыло			
			$headers = 'MIME-Version: 1.0' . "\r\n".
						'Content-type: text/html; charset=utf-8' . "\r\n" .
						'From: '. $requesites->mail_from . "\r\n" .
						'Reply-To: '. $requesites->mail_subject . "\r\n" .
						'X-Mailer: PHP/' . phpversion() . "\r\n" .
						(!empty($this->form['email']) && filter_var($this->form['email'], FILTER_VALIDATE_EMAIL) ? 'BCC: ' . $this->form['email'] . "\r\n" : ''); // Если есть мыло клиента, то пошлем копию ему

			mail($requesites->mail_to, $requesites->mail_subject, $message, $headers);
			
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

		$data['outer_tariff_id'] = $data['tariff'];
		$data['outer_tariff_name'] = $data['tariff_name'];
	    $data['outer_city_from_id'] = $data['city_from_id'];
	    $data['outer_city_from_name'] = $data['city_from'];
	    $data['outer_city_to_id'] = $data['city_to_id'];
	    $data['outer_city_to_name'] = $data['city_to'];
	    $data['mem'] = $data['comments'];

		return $this->store($data);
	}


	// Получение настроек модуля
	function GetSettings()
	{		
		if(!$this->settings) {
			$db = JFactory::getDBO();
			$query = "
select 
	mail_to,
	mail_from,
	mail_subject,
	interest,
	weight_no_size,
	ceil_to
from #__cdek_settings
";
			$db->setQuery($query);
			$this->settings = $db->loadObject();
		}
		return $this->settings;
	}

	// Получение параметров пользователя
	function GetUserSettings()
	{		
		if(!$this->user_settings) {
			$user_id = JFactory::getUser()->id;
			
			$db = JFactory::getDBO();
			$query = "
select 
	with_nds,
	interest
from #__delivery_user
where 
	user = '" . $user_id . "'
";
			$db->setQuery($query);
			$this->user_settings = $db->loadObject();
		}
		return $this->user_settings;
	}

	// Получение списка тарифов
	function GetTariffs()
	{
		$db = JFactory::getDBO();
		$query = "
select 
	s.tariff_id,
	s.tariff_name
from #__cdek_tariff s
where s.published = 1
";
		$db->setQuery($query);
		$result = $db->loadObjectList();

		return $result;
	}
}
?>
