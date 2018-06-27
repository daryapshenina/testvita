<?php
defined('AUTH') or die('Restricted access');

// Функция загрузки изображения
// $act - тип действия: 1 - умный ресайз; 2 - подрезка; 3 - скукожить; 0 - выделенная область; по умолчанию = 1
// $img_name - наименование малого изображения (большое аналогично малому, только с перфиксом _)
// $img_dir - рабочая директория
// $tmp_file - исходный, временный файл - полный путь
// $w_small - ширина малого изображения
// $h_small - высота малого изображения
// $w_big - ширина большого изображения
// $h_big - высота большого изображения
// x1, $y1, $x2, $y2, $w, $h, $w_scale  - область выделения на большом изображении; ширина и высота большого изображения; пропорция по ширине

// выставить лимит в 512Mb
ini_set('memory_limit', '512M');



function img_load($act, $img_name, $img_dir, $tmp_file, $w_small = 200, $h_small = 200, $w_big = 640, $h_big = 480, $x1 = 0, $y1 = 0, $x2 = 200, $y2 = 200, $w = 640, $h = 480, $w_scale = 1)
{
	global $root;
	
	// $src_x, $src_y - координаты на исходном изображении
	// $src_width2, $src_height2 - размер исходного изображения	
	// $dst_x, $dst_y - координаты на конечном изображении
	// $tn_width2, $tn_height2 - размер конечного изображения	
	
	// Получим размер изображения и его тип
	$size = getimagesize($tmp_file); 
	
	$src_width = $size[0];
	$src_height = $size[1];
	$type = $size[2];
	
	// отступы для центрирования малого изображения
	$dst_x = 0;
	$dst_y = 0;

	// отступы внутри источника изображения
	$src_x = 0;
	$src_y = 0;
	
	if($type == 2){$cop = imagecreatefromJpeg($tmp_file);} // jpg
	elseif($type == 3){$cop = imagecreatefrompng($tmp_file);} // png
	elseif($type == 1){$cop = imagecreatefromgif($tmp_file);} // gif
	else
	{
		//если залито что то не то, то он пошлёт нафиг и удалит залитое
		if(file_exists($tmp_file))
		{
			@chmod($tmp_file,0755);
			unlink($tmp_file);
		}
		
		die(
			'
			<!DOCTYPE html>
			<html>
			<head>
			<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
			<title>Ошибка</title>
			</head>	
			<body>
			НЕ ПРАВИЛЬНЫЙ ФОРМАТ ИЗОБРАЖЕНИЯ		
			</body>
			</html>
			'
		); 
	}
	

	
	// ------- Пересчёт размера для большого изображения --------
	$x_ratio = $w_big / $src_width;
	$y_ratio = $h_big / $src_height;
	if (($src_width <= $w_big) && ($src_height <= $h_big))
	{
		$tn_width = $src_width;
		$tn_height = $src_height;
	}
	else if (($x_ratio * $src_height) < $h_big)
	{
		$tn_height = $x_ratio * $src_height;
		$tn_width = $w_big;
	}
	else
	{
		$tn_width = $y_ratio * $src_width;
		$tn_height = $h_big;
	}

	
	
	// ------- Умный ресайз ---------------------------------
	if ($act == 1)
	{
		// --- Пересчёт размера для маленького изображения 
		$x_ratio2 = $w_small / $src_width;
		$y_ratio2 = $h_small / $src_height;
		
		// если "большое изображение" меньше "малого изображения"
		if ( ($src_width <= $w_small) && ($src_height <= $h_small) )
		{
			$tn_width2 = $src_width;
			$tn_height2 = $src_height;
		}
		// находим меньшую сторону - высота меньше ширины (с учётом пропорциональности)
		else if (intval($x_ratio2 * $src_height) <= $h_small)
		{
			$tn_height2 = round($x_ratio2 * $src_height);
			$tn_width2 = $w_small;

			$dst_y = round(($h_small - $tn_height2)/2);				
		}
		// ширина меньше высоты
		else
		{
			$tn_width2 = round($y_ratio2 * $src_width);
			$tn_height2 = $h_small;
			
			$dst_x = round(($w_small - $tn_width2)/2);		
		}
		
		$src_width2 = $src_width;
		$src_height2 = $src_height;	

		$src_x = 0;
		$src_y = 0;	
	}
	// ------- / умный ресайз / -------------------------------


	
	// -------- Подрезка --------------------------------------
	if($act == 2)
	{
		// ------- Расчёт пропорций -------
		// вставляем в малое изображение с начала
		$dst_y = 0; 
		$dst_x = 0;
		
		// на всю ширину малого изображения
		$tn_width2 = $w_small;			
		$tn_height2 = $h_small;	


		// Находим соотношение сторон большого и малого изображения; там, где соотношение меньше, по той стороне и вставляем.
		$x_ratio2 = $src_width / $w_small;
		$y_ratio2 = $src_height / $h_small;
		
		if($x_ratio2 < $y_ratio2) // вставляем по ширине
		{
			$src_x = 0; // от самой левой точки
			
			$src_width2 = $src_width; // ширина - из настроек
			
			$scale = $src_width2 / $tn_width2; // пропорции
			
			$src_height2 = intval($tn_height2 * $scale); // высота прямоугольника из исходного изображения
			
			$src_y = intval(($src_height - $src_height2)/2); // отступ с верху: от высоты исходного изображения отнимаем выбранную область и центрируем по высоте -> /2			
		}
		else // вставляем по высоте
		{
			$src_y = 0; // от самого верха
			
			$src_height2 = $src_height; // высота - из настроек
			
			$scale = $src_height2 / $tn_height2; // пропорции
			
			$src_width2 = intval($tn_width2 * $scale); // высота прямоугольника из исходного изображения
			
			$src_x = intval(($src_width - $src_width2)/2); // отступ с верху: от высоты исходного изображения отнимаем выбранную область и центрируем по высоте -> /2				
		}
	}
	// -------- / подрезка / ----------------------------------


	
	// ------- Скукожить -------------------------------------
	if ($act == 3)
	{
		$tn_width2 = $w_small;
		$tn_height2 = $h_small;

		$src_width2 = $src_width;
		$src_height2 = $src_height;		
	}
	// ------- / скукожить / ---------------------------------



	// ------- Выделенная область -----------------------------
	if ($act == 0)
	{
		// ------- Расчёт пропорций -------
		// Коэффициент сжатия определяем по ширине
		$k = $src_width / $w_scale;
		$src_x = $x1 * $k;
		$src_y = $y1 * $k;	
		$src_width2 = $w * $k;
		$src_height2 = $h * $k;
		
		// ------- Если ява-скрипт не сработал -------
		if ($w == 0)
		{
			// находим меньшую сторону - высота меньше ширины (с учётом пропорциональности)
			if ($src_width > $src_height)
			{	
				$src_x = ($src_width - $src_height)/2;
				$src_y = 0;
				$src_height2 = $src_height;
				$src_width2 = $src_height;
			}
			else
			{
				$src_x = 0;
				$src_y = ($src_height - $src_width)/2;
				$src_height2 = $src_width;
				$src_width2 = $src_width;	
			}	
		}
	}
	// ------- / Выделенная область / ---------------------------

	// ********************
	// $file = $root.'/components/shop/img_upload_ajax.txt';
	// $file_content = "";
	// file_put_contents($file, $file_content);
	// ********************		

	$img_name_big = str_replace ('.jpg', '_.jpg', $img_name);
	
	// --- теперь преобразуем его в новое изображение -------------------------------------------------
	// большое изображение
	$trumb = imagecreatetruecolor($tn_width, $tn_height);
	$white = imagecolorallocate($trumb, 255, 255, 255); // белый фон
	imagefilledrectangle($trumb, 0, 0, $w_big-1, $h_big-1, $white); // рисуем белый прямоугольник
	$image=imagecopyresampled($trumb, $cop, 0, 0, 0, 0, $tn_width, $tn_height, $src_width, $src_height);
	
	
	// Прямоугольная область из исходного изображения $src_width2, $src_height2
	// Координаты на исходном изображении $src_x, $src_y
	// Помещаем в прямоугольный участок $tn_width2, $tn_height2
	// На координатах $dst_x, $dst_y
	// малое изображение
	$trumb2 = imagecreatetruecolor($w_small, $h_small);
	$white2 = imagecolorallocate($trumb2, 255, 255, 255); // белый фон
	imagefilledrectangle($trumb2, 0, 0, $w_small-1, $h_small-1, $white2); 	// рисуем белый прямоугольник
	
	$image2 = imagecopyresampled($trumb2, $cop, $dst_x, $dst_y, $src_x, $src_y, $tn_width2, $tn_height2, $src_width2, $src_height2);
	
	//$image2 = imagecopyresampled($trumb2, $cop, $dst_x, $dst_y, $src_x, $src_y, $tn_width2, $tn_height2, $src_width2, $src_height2);
	
	// --- параметром (50) мы уменьшаем качество изображения.чтобы этого не делать поставьте "-1" -----
	ImageJpeg($trumb,$img_dir.$img_name_big,100);
	ImageJpeg($trumb2,$img_dir.$img_name,100);
	
	//освобождаем память и удаляем временный файл
	ImageDestroy($trumb);
	ImageDestroy($cop);
	
	if(file_exists($tmp_file))
	{
		@chmod($tmp_file,0755);
		unlink($tmp_file);	
	}
	
	$img_arr[0] = $img_name;
	$img_arr[1] = $img_name_big;
	
	return $img_arr;
}

?>