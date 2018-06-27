<?php
defined('AUTH') or die('Restricted access');

// находим старые фотографии
$sql_item = $db->query("SELECT id, section, photo, photo_big, photo_more FROM com_shop_item WHERE cdate < DATE_SUB(NOW(), INTERVAL 1 DAY);");

$photo_dir = $root.'/components/shop/photo/';

foreach($sql_item as $item)
{
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
	$stmt_delete->execute(array('id' => $item['id']));

	// Удаляем характеристики
	$stmt_char_delete = $db->prepare('DELETE FROM com_shop_char WHERE item_id = :item_id');
	$stmt_char_delete->execute(array('item_id' => $item['id']));

	// Удаляем сопутствующие товары
	$stmt_related_delete = $db->prepare('DELETE FROM com_shop_related_item WHERE item_id = :item_id OR related_id = :item_id');
	$stmt_related_delete->execute(array('item_id' => $item['id']));

	// Удаляем дополнительные цены
	$stmt_char_delete = $db->prepare('DELETE FROM com_shop_price_item WHERE item_id = :item_id');
	$stmt_char_delete->execute(array('item_id' => $item['id']));
}

function a_com()
{
	echo '
		<h1>Товары с датой актуализации больше 1 дня удалены</h1>
		<a href="/admin/com/shop/yml" class="greenbutton">Назад</a>
	';
}
