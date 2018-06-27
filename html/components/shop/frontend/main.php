<?php
defined('AUTH') or die('Restricted access');

include_once($root."/lib/currency.php");

CCurrency::update();

include_once($root."/components/shop/classes/classShopSettings.php");

if(!isset($shopSettings))
{
	$s = new classShopSettings;
	$shopSettings = unserialize($s->settings);	
}

$head->addFile('/components/shop/frontend/tmp/style.css');
$head->addFile('/components/shop/frontend/shop_script.js');

// ===================================================================================

// вывод всех разделов интернет-магазина
// if($d[1] == "all"){include($root."/components/shop/frontend/shop.php");}
// аккаунт пользователя
if($d[1] == "account"){include($root."/components/shop/frontend/account/main.php");}

// вывод раздела
elseif($d[1] == "section" || $d[1] == "all"){include($root."/components/shop/frontend/section/section.php");}

// вывод товара
elseif($d[1] == "item"){include($root."/components/shop/frontend/item/item.php");}

// вывод электронного товара
elseif($d[1] == "eitem"){include($root."/components/shop/frontend/item/eitem.php");}

// корзина !!!!! #####################################################################################
elseif($d[1] == "basket"){include($root."/components/shop/frontend/basket/main.php");}

// корзина
//elseif($d[1] == "basket" && $d[2] == "add"){include($root."/components/shop/frontend/shop_basket.php");}

// корзина
//elseif($d[1] == "basket" && $d[2] == ""){include($root."/components/shop/frontend/shop_basket.php");}

// корзина - удалить товар
//elseif($d[1] == "basket" && $d[2] == "del"){include($root."/components/shop/frontend/shop_basket_delete.php");}

// корзина - удалить товар на ajax
//elseif($d[1] == "basket" && $d[2] == "delete_ajax"){include($root."/components/shop/frontend/shop_basket_delete_ajax.php");}

// корзина - оформление товара
//elseif($d[1] == "basket" && $d[2] == "client"){include($root."/components/shop/frontend/shop_basket_client.php");}

// корзина - выбрать способ оплаты
//elseif($d[1] == "basket" && $d[2] == "pay"){include($root."/components/shop/frontend/basket/pay.php");}

// корзина - выбрать способ оплаты

/*
elseif($d[1] == "basket" && $d[2] == "pay2")
{
	// метод платежа
	$paymethod = $_POST['paymethod'];

	if(!isset($paymethod))
	{
		Header ('Location: http://'.$domain);
		exit;
	}

	// Наличными при получении || Наложенным платежём || Предоплата
	if($paymethod == 1 || $paymethod == 2 || $paymethod == 5)
	{
		include($root."/components/shop/frontend/basket/mail.php");
	}

	// QIWI
	if($paymethod == 3)
	{
		include($root."/components/shop/frontend/basket/qiwi/basket.php");
	}

	// Картами (Yandex)
	if($paymethod == 41 || $paymethod == 42)
	{
		include($root."/components/shop/frontend/basket/yandex/yandex.php");
	}
}
*/

// корзина - выбрать способ оплаты
//elseif($d[1] == "basket" && $d[2] == "qiwihelp"){include($root."/components/shop/frontend/shop_basket_qiwihelp.php");}

// корзина - отправить уведомление на email
//elseif($d[1] == "basket" && $d[2] == "mail"){include($root."/components/shop/frontend/basket/mail.php");}

// корзина - успешный платёж QIWI
//elseif($d[1] == "basket" && $d[2] == "qiwisuccess"){include($root."/components/shop/frontend/shop_basket_qiwisuccess.php");}

// корзина - успешный платёж Yandex
//elseif($d[1] == "basket" && $d[2] == "yandexsuccess"){include($root."/components/shop/frontend/shop_basket_yandexsuccess.php");}

// Задать вопрос по товару
elseif($d[1] == "question"){include($root."/components/shop/frontend/question.php");}

// Заказ - лендинг
elseif($d[1] == "landing_order"){include($root."/components/shop/frontend/basket/landing/mail.php");}

// YML
elseif($d[1] == "yml"){include($root."/components/shop/frontend/yml/yml.php");}

// если прочая неопределённая хрень - вывод страницы ошибки
else
{
	header("HTTP/1.0 404 Not Found");
	include("404.php");
	exit;
}



?>
