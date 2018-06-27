<?php
// DAN 2012
// Удаление страницы

defined('AUTH') or die('Restricted access');

$item_id = intval($admin_d4); // преобразуем в число

// находим категорию
$query_item = mysql_query("SELECT * FROM `com_quote_item` WHERE `id` = '$item_id' LIMIT 1") or die ("Невозможно сделать выборку из таблицы - 1");

while($n = mysql_fetch_array($query_item)):
	$section = $n['section_id'];
endwhile; 


mysql_query("DELETE FROM `com_quote_item` WHERE `id` = '$item_id'");	
	
Header ('Location: http://'.$site.'/admin/com/quote/section/'.$section); exit;

?>