<?php
defined('AUTH') or die('Restricted access');

$id = intval($d[5]); // преобразуем в число

// находим старые фотографии
$stmt_item = $db->prepare('SELECT section, photo, photo_big, photo_more FROM com_shop_item WHERE id = :id LIMIT 1');
$stmt_item->execute(array('id' => $id));
$item = $stmt_item->fetch();

$photo_dir = $root.'/components/shop/photo/';

// если есть файл изображения и его имя не пустое - удяляем файлы изображения
if (isset($item['photo']) && $item['photo'] != "")
{
	if(is_file($photo_dir.$item['photo']))
	{
		unlink($photo_dir.$item['photo']);
	}

	if(is_file($photo_dir.$item['photo_big']))
	{
		unlink($photo_dir.$item['photo_big']);
	}
}

$photo_more_arr = explode(';', $item['photo_more']);

for ($i = 0; $i < count($photo_more_arr); $i++)
{
	if(is_file($photo_dir.$photo_more_arr[$i]))
	{
		unlink($photo_dir.$photo_more_arr[$i]);
		unlink(str_replace('.jpg', '_.jpg', $photo_dir.$photo_more_arr[$i]));
	}
}

$stmt_delete = $db->prepare('DELETE FROM com_shop_item WHERE id = :id');
$stmt_delete->execute(array('id' => $id));

// Удаляем характеристики
$stmt_char_delete = $db->prepare('DELETE FROM com_shop_char WHERE item_id = :item_id');
$stmt_char_delete->execute(array('item_id' => $id));

// Удаляем сопутствующие товары
$stmt_related_delete = $db->prepare('DELETE FROM com_shop_related_item WHERE item_id = :item_id OR related_id = :item_id');
$stmt_related_delete->execute(array('item_id' => $id));

// Удаляем дополнительные цены
$stmt_char_delete = $db->prepare('DELETE FROM com_shop_price_item WHERE item_id = :item_id');
$stmt_char_delete->execute(array('item_id' => $id));

if(isset($d[6]) && $d[6] == 'frontend'){Header ("Location: /shop/section/".$item['section']);}
else{Header ("Location: /admin/com/shop/section/".$item['section']);}

?>