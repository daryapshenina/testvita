<?php
// DAN обновление - январь 2014
// Удаление страницы

defined('AUTH') or die('Restricted access');

$item_id = intval($admin_d4); // преобразуем в число
$menu_t = intval($admin_d5); // преобразуем в число

// находим категорию
$query_item = mysql_query("SELECT * FROM `com_article_item` WHERE `id` = '$item_id' LIMIT 1") or die ("Невозможно сделать выборку из таблицы - 1");

while($n = mysql_fetch_array($query_item)):
	$section = $n['section'];
endwhile; 


mysql_query("DELETE FROM com_article_item WHERE id=$item_id");	

// удаляем sef
mysql_query("DELETE FROM `url` WHERE `url`='article/item/$item_id '") or die ("Удаление не возможно - 5");
	
Header ('Location: http://'.$site.'/admin/com/article/section/'.$section.'/'.$menu_t); exit;

?>