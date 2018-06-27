<?php
// DAN 2012
// Удаление товара

defined('AUTH') or die('Restricted access');

$item_id = intval($admin_d4); // преобразуем в число
$menu_t = intval($admin_d5);
	
// находим старые фотографии
$query_item = mysql_query("SELECT * FROM `com_shop_item` WHERE `id` = '$item_id' LIMIT 1") or die ("Невозможно сделать выборку из таблицы - 1");

while($n = mysql_fetch_array($query_item)):
	$section = $n['section'];
	$photo_old_name = $n['photo']; 
	$photobig_old_name = $n['photobig']; 
endwhile; 

// удаляем старые фотографии
$photo_dir = 'components/shop/photo/'; 
$photo_old = $photo_dir.$photo_old_name;
$photobig_old = $photo_dir.$photobig_old_name;
// если есть файл изображения и его имя не пустое - удяляем файлы изображения
if (isset($photobig_old_name) && $photobig_old_name != "")
{
	unlink($photo_old);			
	unlink($photobig_old);
}

mysql_query("DELETE FROM `com_shop_item` WHERE `id` = '$item_id'");	
	
Header ('Location: http://'.$site.'/admin/com/shop/section/'.$section.'/'.$menu_t); exit;

?>