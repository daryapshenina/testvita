<?php
// DAN обновление - январь 2014
// Настройки интернет магазина

defined('AUTH') or die('Restricted access');

$shopemail = $_POST["shopemail"];
$xsmall= intval($_POST["xsmall"]);
$ysmall= intval($_POST["ysmall"]);
$xbig= intval($_POST["xbig"]);
$ybig= intval($_POST["ybig"]);
$smallresizemethod = intval($_POST["smallresizemethod"]);
$quantity = intval($_POST["quantity"]);
$mapping = intval($_POST["mappingmethod"]);
$sorting_items = intval($_POST["sorting_items"]);
$section_description = intval($_POST["section_description"]);
$output_un_section = intval($_POST["output_un_section"]);
$view_item_card = intval($_POST["view_item_card"]);
$button_question = intval($_POST["button_question"]);
$item_quantity = intval($_POST["item_quantity"]);
$basket_type = intval($_POST["basket_type"]);

// Условие - отменить
if ($none == "Отменить"){Header ("Location: http://".$site."/admin/com/shop/all"); exit;}
else {	
	
	// Обновляем данные в таблице "com_shop_settings"
	$query_update_shop = "UPDATE `com_shop_settings` SET `parametr`='$shopemail' WHERE `name`='email'";
	$sql_page = mysql_query($query_update_shop) or die ("Невозможно обновить данные 1");	
	
	$query_update_shop = "UPDATE `com_shop_settings` SET `parametr`='$xsmall' WHERE `name`='x_small'";
	$sql_page = mysql_query($query_update_shop) or die ("Невозможно обновить данные 2");
	
	$query_update_shop = "UPDATE `com_shop_settings` SET `parametr`='$ysmall' WHERE `name`='y_small'";
	$sql_page = mysql_query($query_update_shop) or die ("Невозможно обновить данные 3");

	$query_update_shop = "UPDATE `com_shop_settings` SET `parametr`='$xbig' WHERE `name`='x_big'";
	$sql_page = mysql_query($query_update_shop) or die ("Невозможно обновить данные 4");
	
	$query_update_shop = "UPDATE `com_shop_settings` SET `parametr`='$ybig' WHERE `name`='y_big'";
	$sql_page = mysql_query($query_update_shop) or die ("Невозможно обновить данные 5");	
	
	$query_update_shop = "UPDATE `com_shop_settings` SET `parametr`='$smallresizemethod' WHERE `name`='small_resize_method'";
	$sql_page = mysql_query($query_update_shop) or die ("Невозможно обновить данные 6");
	
	$query_update_shop = "UPDATE `com_shop_settings` SET `parametr`='$quantity' WHERE `name`='quantity'";
	$sql_page = mysql_query($query_update_shop) or die ("Невозможно обновить данные 7");	
	
	$query_update_shop = "UPDATE `com_shop_settings` SET `parametr`='$mapping' WHERE `name`='mapping'";
	$sql_page = mysql_query($query_update_shop) or die ("Невозможно обновить данные 8");
	
	$query_update_shop = "UPDATE `com_shop_settings` SET `parametr`='$sorting_items' WHERE `name`='sorting_items'";
	$sql_page = mysql_query($query_update_shop) or die ("Невозможно обновить данные 9");
	
	$query_update_shop = "UPDATE `com_shop_settings` SET `parametr`='$section_description' WHERE `name`='section_description'";
	$sql_page = mysql_query($query_update_shop) or die ("Невозможно обновить данные 10");	

	$query_update_shop = "UPDATE `com_shop_settings` SET `parametr`='$output_un_section' WHERE `name`='output_un_section'";
	$sql_page = mysql_query($query_update_shop) or die ("Невозможно обновить данные 11");
	
	$query_update_shop = "UPDATE `com_shop_settings` SET `parametr`='$view_item_card' WHERE `name`='view_item_card'";
	$sql_page = mysql_query($query_update_shop) or die ("Невозможно обновить данные 12");

	$query_update_shop = "UPDATE `com_shop_settings` SET `parametr`='$button_question' WHERE `name`='question'";
	$sql_page = mysql_query($query_update_shop) or die ("Невозможно обновить данные 13");
	
	$query_update_shop = "UPDATE `com_shop_settings` SET `parametr`='$item_quantity' WHERE `name`='item_quantity'";
	$sql_page = mysql_query($query_update_shop) or die ("Невозможно обновить данные 14");
	
	$query_update_shop = "UPDATE `com_shop_settings` SET `parametr`='$basket_type' WHERE `name`='basket_type'";
	$sql_page = mysql_query($query_update_shop) or die ("Невозможно обновить данные 15");		
		
} // конец условия заполненного пункта меню
	
Header ("Location: http://".$site."/admin/com/shop/all"); exit;

?>
