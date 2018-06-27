<?php
// DAN 2012
// Публикуем статью

defined('AUTH') or die('Restricted access');

$item_id = intval($admin_d4); // преобразуем в число

// Находим раздел
$sectsql = mysql_query("SELECT * FROM `com_article_item` WHERE `id` = '$item_id' ") or die ("Невозможно сделать выборку из таблицы - 1");

$result = mysql_num_rows($sectsql); 
// если есть такой раздел	
if ($result > 0) {
	while($m = mysql_fetch_array($sectsql)):
		$section = $m['section'];			
	endwhile;
}
	
// Обновляем данные в таблице "com_shop_section"
$query_update_section = "UPDATE `com_article_item` SET `pub` = '1' WHERE `id` = '$item_id';";
	
$sql_section = mysql_query($query_update_section) or die ("Невозможно обновить данные 2");	

Header ("Location: /admin/com/article/section/".$section); exit;		

?>