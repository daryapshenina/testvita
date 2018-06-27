<?php
// DAN 2011
// Настройки интернет магазина
defined('AUTH') or die('Restricted access');

$contract = intval($_POST["contract"]);

// Условие - отменить
if ($none == "Отменить"){Header ("Location: http://".$site."/admin/com/shop/all"); exit;}
else {	
	
	// Обновляем данные в таблице "com_shop_settings"	
	$query_update_shop = "UPDATE `com_shop_settings` SET `parametr`='$contract' WHERE `name`='contract'";
	$sql_page = mysql_query($query_update_shop) or die ("Невозможно обновить данные 15");	

		
} // конец условия заполненного пункта меню
	
Header ("Location: http://".$site."/admin/com/shop/all"); exit;

?>