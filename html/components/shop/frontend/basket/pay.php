<?php
defined('AUTH') or die('Restricted access');
include_once($root."/classes/Auth.php");
include_once $root."/components/shop/classes/Orders.php";

$order_id = Orders::getOrderId();
$items = Orders::getItems();

if(count($items) <= 0){Header ('Location: /'.$domain); exit;}


function component()
{
	global $domain, $root, $shopSettings;	

	if(isset($_POST['tel']))
	{
		$tel = $_POST['tel'];
		if(strlen($tel) < 3){echo "<h1>".LANG_FIELD_PHONE_ERROR."</h1>"; exit;}
	}

	$fio = $_POST['fio'];
	if(Auth::check()){$user = Auth::getUser(); $email = $user['email'];} else{$email = $_POST['email'];}
	$address = $_POST['address'];
	$comments = $_POST['comments'];

	// Подключаем шаблон корзины
	include($root."/components/shop/frontend/basket/tmp/pay_tmp.php");

} // конец функции component


?>