<?php
// DAN 2011
// Настройки интернет магазина

defined('AUTH') or die('Restricted access');

$payment = intval($_POST["payment"]);
$paymentmethod_nal = intval($_POST["paymentmethod_nal"]);
$paymentmethod_pred = intval($_POST["paymentmethod_pred"]);
$paymentmethod_np = intval($_POST["paymentmethod_np"]);
$paymentmethod_qiwi = intval($_POST["paymentmethod_qiwi"]);
$paymentmethod_yandex = intval($_POST["paymentmethod_yandex"]);
$qiwi_id = intval($_POST["qiwi_id"]);
$yandex_id = intval($_POST["yandex_id"]);
$yandex_secret = strip_tags($_POST["yandex_secret"]);

$paymentmethod = $paymentmethod_nal.','.$paymentmethod_np.','.$paymentmethod_qiwi.','.$paymentmethod_yandex.','.$paymentmethod_pred;



// Условие - отменить
if ($none == "Отменить"){Header ("Location: http://".$site."/admin/com/shop/all"); exit;}
else {	
	
	// Обновляем данные в таблице "com_shop_settings"
	
	$query_update_shop = "UPDATE `com_shop_settings` SET `parametr`='$payment' WHERE `name`='payment'";
	$sql_page = mysql_query($query_update_shop) or die ("Невозможно обновить данные 1");
	
	$query_update_shop = "UPDATE `com_shop_settings` SET `parametr`='$paymentmethod' WHERE `name`='paymentmethod'";
	$sql_page = mysql_query($query_update_shop) or die ("Невозможно обновить данные 2");

	$query_update_shop = "UPDATE `com_shop_settings` SET `parametr`='$qiwi_id' WHERE `name`='qiwi_id'";
	$sql_page = mysql_query($query_update_shop) or die ("Невозможно обновить данные 3");
	
	$query_update_shop = "UPDATE `com_shop_settings` SET `parametr`='$yandex_id' WHERE `name`='yandex_id'";
	$sql_page = mysql_query($query_update_shop) or die ("Невозможно обновить данные 4");
	
	$query_update_shop = "UPDATE `com_shop_settings` SET `parametr`='$yandex_secret' WHERE `name`='yandex_secret'";
	$sql_page = mysql_query($query_update_shop) or die ("Невозможно обновить данные 5");

		
} // конец условия заполненного пункта меню
	
Header ("Location: http://".$site."/admin/com/shop/all"); exit;

?>