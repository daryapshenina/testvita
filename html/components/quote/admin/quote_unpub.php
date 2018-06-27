<?php
// DAN 2012
// Снимаем с публикации пункт меню $admin_d2 = $d[2];

defined('AUTH') or die('Restricted access');

// № пункта преобразуем в число
$id_com = intval($admin_d4); 
	
// Обновляем данные в таблице "menu"
$query_update_menu = "UPDATE `menu` SET `pub` = '0' WHERE `id_com` = '$id_com' AND `component` = 'quote' AND `main` = '1';";
	
$sql_menu = mysql_query($query_update_menu) or die ("Невозможно обновить данные 2");	

Header ("Location: http://".$site."/admin/site/"); exit;		

?>