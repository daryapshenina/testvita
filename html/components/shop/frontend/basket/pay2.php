<?php
defined('AUTH') or die('Restricted access');
include_once($root."/classes/Auth.php");

if(!isset($_POST['paymethod']))
{
	// Header ('Location: /'.$domain);
	echo "<h1>Не выбран вариант оплаты</h1>";
	exit;
}

if(isset($_POST['tel']))
{
	$tel = $_POST['tel'];
	$tel = '+'.preg_replace('/[^0-9]/', '', $tel);
	$tel = str_replace('+8', '+7', $tel);

	if(strlen($tel) < 10){echo "<h1>".LANG_FIELD_PHONE_ERROR."</h1>";exit;}
}
else
{
	$tel = '';
}

$paymethod = $_POST['paymethod'];
$fio = $_POST['fio'];
if(Auth::check()){$user = Auth::getUser(); $email = $user['email'];}else{$email = $_POST['email'];}	
$address = $_POST['address'];
$comments = $_POST['comments'];

if($paymethod == 'cash' || $paymethod == 'prepayment' || $paymethod == 'сash_on_delivery'){include($root."/components/shop/frontend/basket/mail.php");} // Наличными при получении || Предоплата || Наложенным платежём
if($paymethod == 'yandex_money') include($root."/components/shop/frontend/basket/yandex/yandex_money.php"); // Картами (Yandex)
if($paymethod == 'yandex_cashbox') include($root."/components/shop/frontend/basket/yandex/yandex_cashbox.php"); // Яндекс касса
if($paymethod == 'sberbank') include($root."/components/shop/frontend/basket/sberbank/sberbank.php"); // Яндекс касса
?>