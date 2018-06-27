<?php
defined('AUTH') or die('Restricted access');

$id = intval($_POST['id']);
$img_name = $_POST['img_name'];

$stmt_item = $db->prepare("SELECT photo, photo_more FROM com_shop_item WHERE id = :id LIMIT 1");
$stmt_item->execute(array('id' => $id));

while($m = $stmt_item->fetch())
{
	// если это главная фоторафия - удаляем её и на её место ставим первую фотографию из списка, если такая присутствует
	if ($img_name == $m['photo'])
	{
		if ($m['photo_more'] != '')
		{
			$photomore_arr = explode(';', $m['photo_more']);
			$photo_main_new_small = $photomore_arr[0]; // следующая фотография за главной
			$photo_main_new_big = str_replace('.jpg', '_.jpg', $photo_main_new_small);
			$photomore_new = str_replace($photomore_arr[0].';', '', $m['photo_more']); // удаляем следующую за главной фотографию, т.к. она становиться главной	
		}
		else
		{
			$photo_main_new_small = '';
			$photo_main_new_big = '';
			$photomore_new = '';
		}
		
		$photobig = str_replace('.jpg', '_.jpg', $m['photo']);		
		
		// удаляем старые фотографии
		$photo_dir = $root.'/components/shop/photo/'; 
		$photo_dir_old = $photo_dir.$m['photo'];
		$photobig_dir_old = $photo_dir.$photobig;		

		// если есть файл изображения и его имя не пустое - удаляем файлы изображения
		if (isset($photobig_old) && $photobig_old != "")
		{
			unlink($photo_dir_old);			
			unlink($photobig_dir_old);
		}		
		
		// обновляем базу данных
		$stmt_update_1 = $db->prepare("UPDATE com_shop_item SET photo = :photo, photo_big = :photo_big, photo_more = :photo_more WHERE id = :id LIMIT 1 ;");
		$stmt_update_1->execute(array('photo' => $photo_main_new_small, 'photo_big' => $photo_main_new_big, 'photo_more' => $photomore_new, 'id' => $id));	
	}
	else // если это не главная фотография
	{
		$photomore_new = str_replace($img_name.';', '', $m['photo_more']);
		$photobig_old = str_replace('.jpg', '_.jpg', $img_name);
		
		// удаляем старые фотографии
		$photo_dir = $root.'/components/shop/photo/'; 
		$photo_dir_old = $photo_dir.$img_name;
		$photobig_dir_old = $photo_dir.$photobig_old;

		// если есть файл изображения и его имя не пустое - удяляем файлы изображения
		if (isset($photobig_old) && $photobig_old != "")
		{
			unlink($photo_dir_old);			
			unlink($photobig_dir_old);
		}
	
		// обновляем базу данных
		$stmt_update_1 = $db->prepare("UPDATE com_shop_item SET photo_more = :photo_more WHERE id = :id LIMIT 1 ;");
		$stmt_update_1->execute(array('photo_more' => $photomore_new, 'id' => $id));		
	}
}

exit;

?>
