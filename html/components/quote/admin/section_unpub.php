<?php
// DAN 2012
// Снимаем с публикации пункт меню $admin_d2 = $d[2];

defined('AUTH') or die('Restricted access');

// № пункта преобразуем в число
$id_com = intval($admin_d4); 
	
// Обновляем данные в таблице "menu"
$menu_update_sql = "UPDATE `menu` SET `pub` = '0' WHERE `id_com` = '$id_com' AND `component` = 'quote' AND `p1` = 'section';";
	
$sql_update = mysql_query($menu_update_sql) or die ("Невозможно обновить данные 2");	


// Обновляем данные в таблице "com_quote_section"
$section_update_sql = "UPDATE `com_quote_section` SET `pub` = '0' WHERE `id` = '$id_com'";
	
$section_update = mysql_query($section_update_sql) or die ("Невозможно обновить данные 2");	


Header ("Location: http://".$site."/admin/site"); exit;		

?>