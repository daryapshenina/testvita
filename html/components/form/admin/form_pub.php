<?php
// DAN 2012
// Публикуем пункт меню $admin_d2 = $d[2];

defined('AUTH') or die('Restricted access');

// id_com
$id_com = intval($admin_d4); 
	
// Обновляем данные в таблице "menu"
$query_update_menu = "UPDATE `menu` SET `pub` = '1' WHERE `component` = 'form';";
	
$sql_menu = mysql_query($query_update_menu) or die ("Невозможно обновить данные 1");

Header ("Location: /admin/site/"); exit;		

?>