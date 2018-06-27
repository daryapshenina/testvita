<?php
defined('AUTH') or die('Restricted access');
include_once $root."/components/shop/classes/Orders.php";
include_once $root."/components/shop/frontend/basket/sberbank/sberbankPay.class.php";

$order_id = Orders::getOrderId();
$items = Orders::getItems();

$sber = new sberbankPay();
$answer_arr = $sber->getOrderStatus($order_id);


function component()
{
	global $answer_arr;

	echo '<h1>Платёж завершён с ошибкой!</h1>';
	echo $answer_arr['text'];
}




?>