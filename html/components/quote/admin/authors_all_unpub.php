<?php
// DAN 2012
// Снимаем с публикации пункт меню "Все авторы цитат";

defined('AUTH') or die('Restricted access');
	
// Обновляем данные в таблице "menu"
$menu_update_sql = "UPDATE `menu` SET `pub` = '0' WHERE `component` = 'quote' AND `p1` = 'authors';";	
$sql_update = mysql_query($menu_update_sql) or die ("Невозможно обновить данные 2");	

Header ("Location: http://".$site."/admin/site"); exit;		

?>