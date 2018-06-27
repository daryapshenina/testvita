<?php
defined('AUTH') or die('Restricted access');
include_once($_SERVER['DOCUMENT_ROOT'].'/classes/ImageResize/ImageResizeSelectArea.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/classes/ImageResize/ImageResize.php');

function ads_image_resize($user_id, $ads_id)
{
	global $root;

	// Изображение
	$scale = $_POST['scale'];
	$x1 = intval($_POST['x1']);
	$x2 = intval($_POST['x2']);
	$y1 = intval($_POST['y1']);
	$y2 = intval($_POST['y2']);

	$file_tmp = $_FILES['file']['tmp_name'];
	$file_name = mb_strtolower($_FILES['file']['name']); // Оригинальное имя файла на компьютере клиента.
	$file_type = $_FILES['file']['type']; // Mime-тип файла, в случае, если браузер предоставил такую информацию. Пример: "image/gif".
	$file_size = $_FILES['file']['size']; // Размер в байтах принятого файла.

	if(!empty($file_name))
	{
		// ======= ВСТАВЛЯЕМ ИЗОБРАЖЕНИЕ =======
		$floor_id = 1000 * floor($user_id/1000); // тысячная папка
		$path = $root.'/files/ads/'.$floor_id.'/'.$user_id;

		// Если папка не существует
		if(!is_dir($path)) mkdir($path, 0755, true);

		// --- Проверяем расширение ---
		$file_name_arr = explode('.', $file_name);
		$ext = array_pop($file_name_arr); // Извлекает последний элемент массива, уменьшая его на 1

		if(!($ext == 'jpg' || $ext == 'jpeg' || $ext == 'gif' || $ext == 'png'))
		{
			$err_text = 'Тип файла - не изображение формата jpg, gif, png';
			$err_log = '/components/ads/frontend/my/image_resize.php <br>Ресайз изображения.<br>Тип файла - не изображение. Мим тип <b>'.$file_name.'</b><br>Пользователь <b>'.Auth::check().'<b>';
			err($err_text, $err_log);
		}

		// --- Проверяем тип файла ---
		$size = getimagesize($file_tmp); // Получим размер изображения и его тип
		$src_width = $size[0];
		$src_height = $size[1];
		$type = $size[2];

		if(!($type == 1 || $type == 2 || $type == 3))
		{
			$err_text = 'Тип файла - не изображение формата jpg, gif, png';
			$err_log = '/components/ads/frontend/my/image_resize.php <br>Ресайз изображения.<br>Тип файла - не изображение. Мим тип <b>'.$type.'</b><br>Пользователь <b>'.Auth::check().'<b>';
			err($err_text, $err_log);
		}

		$w = $x2 - $x1;
		$h = $y2 - $y1;

		$img_thumb = new ImageResizeSelectArea($file_tmp, $path.'/'.$ads_id.'.jpg', 200, 150);
		$img_thumb->setArea($x1, $y1, $w, $h);
		$img_thumb->run();

		$img_photo = new ImageResize($file_tmp, $path.'/'.$ads_id.'_.jpg', 800, 800);
		$img_photo->run();		

		return 1;
	}
	else
	{
		return 0;
	}
}
?>