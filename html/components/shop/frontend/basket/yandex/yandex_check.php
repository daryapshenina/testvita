<?php
defined('AUTH') or die('Restricted access');

include_once $root."/components/shop/classes/Orders.php";

if(isset($_POST['action'])){$action = $_POST['action'];} else{$action = '';} // checkOrder

if(isset($_POST['requestDatetime'])){$requestDatetime = $_POST['requestDatetime'];} else{$requestDatetime = '';} // Момент формирования запроса в Яндекс.Кассе.
if(isset($_POST['shopId'])){$shopId = $_POST['shopId'];} else{$shopId = '';}
if(isset($_POST['shopArticleId'])){$shopArticleId = $_POST['shopArticleId'];} else{$shopArticleId = '';} // Идентификатор товара, выдается в Яндекс.Кассе.
if(isset($_POST['invoiceId'])){$invoiceId = $_POST['invoiceId'];} else{$invoiceId = '';} // Уникальный номер транзакции в Яндекс.Кассе.
if(isset($_POST['orderNumber'])){$orderNumber = $_POST['orderNumber'];} else{$orderNumber = '';} // Номер заказа
if(isset($_POST['customerNumber'])){$customerNumber = $_POST['customerNumber'];} else{$customerNumber = '';} // Идентификатор плательщика
if(isset($_POST['orderCreatedDatetime'])){$orderCreatedDatetime = $_POST['orderCreatedDatetime'];} else{$orderCreatedDatetime = '';} // Момент регистрации заказа в Яндекс.Кассе.
if(isset($_POST['orderSumAmount'])){$orderSumAmount = $_POST['orderSumAmount'];} else{$orderSumAmount = '';} // Сумма
if(isset($_POST['orderSumCurrencyPaycash'])){$orderSumCurrencyPaycash = $_POST['orderSumCurrencyPaycash'];} else{$orderSumCurrencyPaycash = '';} // Код валюты для суммы заказа.
if(isset($_POST['orderSumBankPaycash'])){$orderSumBankPaycash = $_POST['orderSumBankPaycash'];} else{$orderSumBankPaycash = '';} // Код процессингового центра в Яндекс.Кассе для суммы заказа.
if(isset($_POST['shopSumAmount'])){$shopSumAmount = $_POST['shopSumAmount'];} else{$shopSumAmount = '';} // Сумма к выплате на расчетный счет магазина (сумма заказа минус комиссия Яндекс.Кассы).
if(isset($_POST['shopSumCurrencyPaycash'])){$currency_code = $_POST['shopSumCurrencyPaycash'];} else{$currency_code = '';} // Код валюты для shopSumAmount.
if(isset($_POST['shopSumBankPaycash'])){$currency_bank = $_POST['shopSumBankPaycash'];} else{$currency_bank = '';} // Код процессингового центра Яндекс.Кассы для shopSumAmount
if(isset($_POST['paymentPayerCode'])){$yandex_account = $_POST['paymentPayerCode'];} else{$yandex_account = '';} // Номер кошелька в Яндекс.Деньгах, с которого производится оплата.
if(isset($_POST['paymentType'])){$payment_type = $_POST['paymentType'];} else{$payment_type = '';} // Способ оплаты заказа. Коды способов оплаты
if(isset($_POST['md5'])){$md5 = $_POST['md5'];} else{err_xml($invoiceId, 'checkOrderResponse');}


$stmt_o = $db->prepare("SELECT user_id FROM com_shop_orders WHERE id = :order_id");
$stmt_o->execute(array('order_id' => $orderNumber));
$user_id = $stmt_o->fetchColumn();


// Получаем массив товаров в заказе
$items = Orders::getItems($orderNumber);

$summa = 0;

foreach($items as $key => $item)
{
	if($item['price'] < 0 || $item['price'] > 999999999) $item['price'] = 0;
	$summa += $item['price'] * $item['quantity'];
}


// Метод checkОrder
if($action == 'checkOrder')
{
	$summa = intval($summa).'.00'; // Округляем до рублей и дописываем '.00' - так требует Яндекс

	// Вычисляем контрольную сумму
	$md5_hash = strtoupper(md5('checkOrder'.';'.$summa.';'.$orderSumCurrencyPaycash.';'.$orderSumBankPaycash.';'.$shopSettings->yandex_cashbox_shop_id.';'.$invoiceId.';'.$user_id.';'.$shopSettings->yandex_cashbox_password));

	if($md5_hash == $md5)
	{
		echo '<?xml version="1.0" encoding="UTF-8"?><checkOrderResponse performedDatetime="'.date('c').'" code="0" invoiceId="'.$invoiceId.'" shopId="'.$shopSettings->yandex_cashbox_shop_id.'"/>';
	}
	else
	{
		err_xml($invoiceId, 'checkOrderResponse');
	}

	exit;
}



// paymentAviso
if($action = 'paymentAviso')
{
	$summa = intval($summa).'.00'; // Округляем до рублей и дописываем '.00' - так требует Яндекс

	// Вычисляем контрольную сумму
	$md5_hash = strtoupper(md5('paymentAviso'.';'.$summa.';'.$orderSumCurrencyPaycash.';'.$orderSumBankPaycash.';'.$shopSettings->yandex_cashbox_shop_id.';'.$invoiceId.';'.$user_id.';'.$shopSettings->yandex_cashbox_password));

	if($md5_hash == $md5)
	{
		// Обновляем статус заказа
		$stmt_update = $db->prepare("UPDATE com_shop_orders SET status = '1', date_order = :date_order WHERE id = :id");
		$stmt_update->execute(array('id' => $orderNumber, 'date_order' => date("Y-m-d H:i:s")));

		// UTM
		$summa_format = number_format($summa, 0, '', ' ');	
		$leads_title = LANG_ORDER_FROM_SHOP.' - '.$summa_format.' '.$shopSettings->currency.' '.LANG_ORDER_NUMBER.' - '.$orderNumber.'. '.LANG_ORDER_YANDEX_CASHBOX_SELECT.'.';


		$stmt_utm = $db->prepare("SELECT id FROM com_leads WHERE title = :title LIMIT 1");
		$stmt_utm->execute(array('title' => $leads_title));
		$lead_id = $stmt_utm->fetchColumn();

		$leads_title_new = LANG_ORDER_FROM_SHOP.' - '.$summa_format.' '.$shopSettings->currency.' '.LANG_ORDER_NUMBER.' - '.$orderNumber.'. '.LANG_ORDER_YANDEX_CASHBOX_SUCCESS.'.';		


		$stmt_utm_update = $db->prepare("UPDATE com_leads SET title = :title WHERE id = :id");
		$stmt_utm_update->execute(array('id' => $lead_id, 'title' => $leads_title_new));			

		echo '<?xml version="1.0" encoding="UTF-8"?><paymentAvisoResponse performedDatetime="'.date("c").'" code="0" invoiceId="'.$invoiceId.'" shopId="'.$shopSettings->yandex_cashbox_shop_id.'"/>';

		ini_set('display_errors', 'Off');

		/* Отправка */
		// Ищем оплаченный заказ в БД
		$stmt_order = $db->prepare("SELECT id, orders, sum, fio, tel, email, address, comments FROM com_shop_orders WHERE id = :id");
		$stmt_order->execute(array('id' => $orderNumber));
		$order = $stmt_order->fetch();

		$items_email_out = $order['orders'];
		$fio = $order['fio'];
		$tel = $order['tel'];
		$email = $order['email'];
		$address = $order['address'];
		$comments = $order['comments'];
		$paymethod_type = 'Яндекс - Касса.';


		include_once $root."/components/shop/frontend/basket/tmp/mail.php";		
		$email_shop = Settings::instance()->getValue('email');

		if(isset($domain_idn)){$dmn = $domain_idn;}else{$dmn = $domain;}
		$from = 'no-replay@'.$dmn;
		$subject = LANG_ORDER_FROM_SHOP." ".$dmn;


		classMail::send($email_shop, $from, $subject, $email_content, null);
		classMail::send($email, $from, $subject, $email_content, null);

		exit;		
	}
	else
	{
		err_xml($invoiceId, 'paymentAvisoResponse');
	}

	exit;
}


function err_xml($invoiceId, $act)
{
	global $shopSettings;
	echo '<?xml version="1.0" encoding="UTF-8"?><'.$act.' performedDatetime="'.date("c").'" code="1" invoiceId="'.$invoiceId.'" shopId="'.$shopSettings->yandex_cashbox_shop_id.'" message="Значение параметра md5 не совпадает с результатом расчета хэш-функции"/>';
	exit;
}


// log
function ya_log($_text)
{
	global $root;
	$str = $_text."\n";
	$file = $root.'/components/shop/frontend/basket/yandex/log.txt';
	$f = fopen($file,"a+");
	fwrite($f,$str);
	fclose($f);		
}

?> 