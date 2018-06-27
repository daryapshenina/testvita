<?php
defined('AUTH') or die('Restricted access');
include_once $root."/components/shop/classes/Orders.php";
include_once $root."/components/shop/frontend/basket/sberbank/sberbankPay.class.php";

$order_id = Orders::getOrderId();
$items = Orders::getItems($order_id);

$sber = new sberbankPay();
$answer_arr = $sber->getOrderStatus($order_id);

if($answer_arr['result'])
{
	Orders::updateOrderStatus(1);

	/* Отправка */
	// Ищем оплаченный заказ в БД
	$stmt_order = $db->prepare("SELECT id, orders, sum, fio, tel, email, address, comments FROM com_shop_orders WHERE id = :id");
	$stmt_order->execute(array('id' => $order_id));
	$order = $stmt_order->fetch();

	$items_email_out = $order['orders'];
	$fio = $order['fio'];
	$tel = $order['tel'];
	$summa_format = number_format($order['sum'], 0, '', ' ');	
	$email = $order['email'];
	$address = $order['address'];
	$comments = $order['comments'];

	$paymethod = 'sberbank';

	include_once $root."/components/shop/frontend/basket/tmp/mail.php";		
	$email_shop = Settings::instance()->getValue('email');

	if(isset($domain_idn)){$dmn = $domain_idn;}else{$dmn = $domain;}
	$from = 'no-replay@'.$dmn;
	$subject = LANG_ORDER_FROM_SHOP." ".$dmn;


	classMail::send($email_shop, $from, $subject, $email_content, null);
	classMail::send($email, $from, $subject, $email_content, null);
}


function component()
{
	global $answer_arr;

	if($answer_arr['result']) 
	{
		$t = 'успешно';
		$answer_arr['text'] = '';
	}
	else
	{
		$t = 'с ошибкой';
	}


	echo '<h1>Платёж завершён '.$t.'!</h1>';
	echo $answer_arr['text'];
}

?>