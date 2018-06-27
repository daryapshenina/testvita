<?php
// DAN 2012
// Настройки интернет магазина

defined('AUTH') or die('Restricted access');

$quantity = intval($_POST["quantity"]);
$none = $_POST["none"];

// Условие - отменить
if ($none == "Отменить"){Header ("Location: /admin/com/article/"); exit;}

else {	

	// Обновляем данные в таблице "com_article_settings"
	$query_update_article = "UPDATE `com_article_settings` SET `parametr`='$quantity' WHERE `name`='quantity'";
	$sql_article = mysql_query($query_update_article) or die ("Невозможно обновить данные 1");	
	
	//$query_update_article = "UPDATE `com_article_settings` SET `parametr`='$xsmall' WHERE `name`='x_small'";
	//$sql_item = mysql_query($query_update_article) or die ("Невозможно обновить данные 3");

	Header ("Location: /admin/com/article"); exit;
		
} // конец условия заполненного пункта меню
	
Header ("Location: /admin/com/article"); exit;
 
?>