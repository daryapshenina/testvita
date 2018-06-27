<?php
defined('AUTH') or die('Restricted access');
include_once $root.'/classes/MobileDetector.php';

class sberbankPay {
	// ТЕСТОВЫЕ КАРТЫ >>> В качестве Cardholder name указывать от 2 слов в английской раскладке.
	// Для всех карт, вовлеченных в 3d Secure ( veres=y, pares=y или a ) пароль на ACS: 12345678.
	/*
	"Заглушка" в самом шлюзе
	"Stub" in the payment gate:"Stub" in the payment gate:
	pan: 4111 1111 1111 1111
	exp date: 2019/12
	cvv2: 123
	*/


	public $login = '';
	public $pass = '';
	public $test = 1;
	public $nds = 0;
	public $url_register = 'https://securepayments.sberbank.ru/payment/rest/register.do'; // Рабочая среда. Регистрация заказа
	public $url_status = 'https://securepayments.sberbank.ru/payment/rest/getOrderStatus.do'; // Рабочая среда. Получение статуса заказа	
	public $url_register_test = 'https://3dsec.sberbank.ru/payment/rest/register.do'; // ТЕСТ Регистрация заказа
	public $url_status_test = 'https://3dsec.sberbank.ru/payment/rest/getOrderStatus.do'; // ТЕСТ Получение статуса заказа
	public $url_success = '/shop/basket/sberbank/success'; // Адрес в случае успешной оплаты.  Дополняется в конструкторе, например, https://test.ru вместо test.ru
	public $url_fail = '/shop/basket/sberbank/fail'; // Дополняется до полного в конструкторе. Адрес, на который требуется перенаправить пользователя в случае неуспешной оплаты.
	public $device = '';
	public $ip = '62.76.205.3';
	public $ip_test = '95.128.178.93';
	public $port = '443';

	public function __construct()
	{
		global $domain, $shopSettings;

		$this->login = $shopSettings->sberbank_login;
		$this->pass = $shopSettings->sberbank_password;
		$this->test = $shopSettings->sberbank_test;
		$this->nds = $shopSettings->nds;
    	$this->url_success = 'https://'.$domain.$this->url_success;
    	$this->url_fail = 'https://'.$domain.$this->url_fail;
    	$this->device = 'DESKTOP'; // $this->device = MobileDetector::getDevice() ? 'MOBILE' : 'DESKTOP';

    	if($this->test)
    	{
 			$this->url_register = $this->url_register_test;
 			$this->url_status = $this->url_status_test;  		
    	}
	}


    function registerOrder($_order_id, $_amount, $_email, $_tel)
    {
    	global $SITE;


		// ======= GET =======	
		$url = $this->url_register.'?';
		$url .= 'userName='.urlencode($this->login);
		$url .= '&password='.urlencode($this->pass);
		$url .= '&orderNumber='.$_order_id; // Номер заказа
		$url .= '&amount='.$_amount*100; // Сумма платежа в копейках (или центах)
		$url .= '&returnUrl='.$this->url_success;
		$url .= '&failUrl='.$this->url_fail; 
		$url .= '&pageView='.$this->device;
		$url .= '&taxSystem='.$this->nds;
		

		// ======= ОНЛАЙН-КАССА ATOLL =======
		$tel = preg_replace('/[^0-9]/', '', $_tel);
		
		// Получаем массив товаров в заказе
		$items = Orders::getItems($_order_id);
		$summa = 0;

		$orderBundleItems = '';
		
		$i = 1;
		$count = count($items);
	
		foreach($items as $key => $item)
		{
			if($item['price'] < 0 || $item['price'] > 999999999) $item['price'] = 0;
			$summa += $item['price'] * $item['quantity'];

			$orderBundleItems .= '{';
			$orderBundleItems 	.= '"positionId":"'.$i.'",';
			$orderBundleItems 	.= '"name":"'.$item['title'].'",';
			$orderBundleItems 	.= '"quantity":{"value":'.$item['quantity'].',"measure":"'.'шт.'.'"},'; // Единица измерения, не должно быть пустым  != ('' || ' ')
			$orderBundleItems 	.= '"itemAmount":'.$item['price'] * 100 * $item['quantity'].",";
			$orderBundleItems 	.= '"itemCode":"'.$item['id'].'",';
			$orderBundleItems 	.= '"tax": {"taxType":'.$this->nds.'},';
			$orderBundleItems 	.= '"itemPrice":'.$item['price'] * 100;

			$orderBundleItems .= '}';

			if($i < $count) $orderBundleItems .= ', ';

			$i++;
		}		
		
		if($tel != '') $tel_out = '"phone":"'.$tel.'"'; else $tel_out = '';


		$orderBundle = '{';
		$orderBundle 	.= '"orderCreationDate":"'.date("Y-m-d").'T'.date("H:i:s").'",';
		$orderBundle 	.= '"customerDetails":{"email":"'.$_email.'",'.$tel_out.'},'; 	// email, phone
		$orderBundle 	.= '"cartItems":{"items":['.$orderBundleItems.']}'; // Тэг с атрибутами товарных позиции Корзины
		$orderBundle .= '}';

		$url .= '&orderBundle='.urlencode($orderBundle);


//echo $url;
//exit;


        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);


		$response = curl_exec($ch);
        curl_close($ch);

		// $response = '{"orderId":"70906e55-7114-41d6-8332-4609dc6590f4","formUrl":" https://3dsec.sberbank.ru/payment/merchants/test/payment_ru.html?mdOrder=70906e55-7114-41d6-8332-4609dc6590f4 "}';


//print_r($response);
//exit;


        return json_decode($response, true);
    }


    function getOrderStatus ($_order_id)
    {
    	global $db;

    	$stmt = $db->prepare("SELECT payer FROM com_shop_orders WHERE id = :order_id");
    	$stmt->execute(array('order_id' => $_order_id));
    	$sber_order_id = $stmt->fetchColumn();

    	$url = $this->url_status.'?';
		$url .= 'userName='.urlencode($this->login);
		$url .= '&password='.urlencode($this->pass);
		$url .= '&orderId='.$sber_order_id; // Номер заказа в системе сбера

		$ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $response = curl_exec($ch);
        curl_close($ch);

        // $response = '{"expiration":"201512","cardholderName":"trtr","depositAmount":789789,"currency":"643","approvalCode":"123456","authCode":2,"clientId":"777","bindingId":"07a90a5d-cc60-4d1b-a9e6-ffd15974a74f","ErrorCode":"0","ErrorMessage":"","OrderStatus":2,"OrderNumber":"23asdafaf","Pan":"411111**1111","Amount":789789}';

		if($response)
		{
            $r_arr = json_decode($response, true);

            if(isset($r_arr['OrderStatus']) && $r_arr['OrderStatus'] == 2)
            {
                return array('result' => true, 'text' => 'Спасибо, заказ № '.$r_arr['OrderNumber'].' оплачен.');
            } 

            return array('result' => false, 'text' => 'Ошибка №'.$r_arr['ErrorCode'].' Статус заказа: '.$r_arr['ErrorMessage']);
        }
        return false;
    }
}

?>