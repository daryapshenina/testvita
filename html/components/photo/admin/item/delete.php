<?php
defined('AUTH') or die('Restricted access');

$id = intval($d[5]); // преобразуем в число

// находим старые фотографии
$stmt_item = $db->prepare('SELECT section, name FROM com_photo_items WHERE id = :id LIMIT 1');
$stmt_item->execute(array('id' => $id));
$item = $stmt_item->fetch();

$photo_dir = $root.'/files/photo/'.$item['section'].'/'; 

// если есть файл изображения и его имя не пустое - удяляем файлы изображения
if (isset($item['name']) && $item['name'] != "")
{
	if(is_file($photo_dir.$item['name'].'.jpg')) unlink($photo_dir.$item['name'].'.jpg');		
	if(is_file($photo_dir.$item['name'].'_.jpg')) unlink($photo_dir.$item['name'].'_.jpg');
}

$stmt_delete = $db->prepare('DELETE FROM com_photo_items WHERE id = :id');
$stmt_delete->execute(array('id' => $id));

if($d[6] == 'frontend'){Header ('Location: /photo/section/'.$item['section']); exit;}
else{Header ('Location: /admin/com/photo/section/'.$item['section']); exit;}


?>