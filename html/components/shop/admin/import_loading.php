<?php
// DAN 2012
// Импорт данных из 1С

session_start();

include("../../../config.php");
include("../../../lib/lib.php");
include("../../../administrator/login.php");

$pt=$_SERVER['DOCUMENT_ROOT'];

$item_number = intval($_GET["itn"]);
$item_sum = intval($_GET["its"]);

// это отправленное ранее этим же файлом теперь принимаем для обработки.
$item_id = $_POST["item_id"];

$imagefile_name = checkingeditor_2(@$_FILES['imagefile']['name']); // Оригинальное имя файла на компьютере клиента. 
$imagefile_size = checkingeditor_2(@$_FILES['imagefile']['size']); // Размер в байтах принятого файла. 
$imagefile_tmp = @$_FILES['imagefile']['tmp_name']; // Временное имя, с которым принятый файл был сохранен на сервере. 

// время работы скрипта 90 сек.
set_time_limit(90); 

// отключаем отображение ошибок
ini_set('display_errors','Off'); 

if ($imagefile_size >= 400000) // Проверка размера файла
{
	die ('
	<html>
	<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<title>Файл слишком большой!</title>
	</head>
	<body>
	<h3 align="center">Файл слишком большой! Максимальный размер файла не более 400 килобайт.</h3>
	<div>Произведите загрузку заново, с удалением старых записей</div>
	</body>
	</html>
	');
}



if ($item_number >0) // перопускаем 0 ход.
{
	// находим этот элемент в базе данных
	$item_query = mysql_query("SELECT * FROM `com_shop_item` WHERE `id` = '$item_id' LIMIT 1") or die ("Невозможно сделать выборку из таблицы - 2");	
	
	while($p = mysql_fetch_array($item_query)):
		$id = $p['id'];
		$artikul = $p['artikul'];
		$title = $p['title'];
		$photo_prev = $p['photo'];	
	endwhile;
	
	// --- ЕСЛИ ИЗОБРАЖЕНИЕ УКАЗАНО В ПРАЙС - ЛИСТЕ ---
	if ($photo_prev != "")
	{
		// -- ЕСЛИ ИЗОБРАЖЕНИЕ НЕ НАЙДЕНО --
		// изображение не найденно в массиве файлов (пустое значение)
		if ($imagefile_name == "")
		{
			$warning = 
			'
			<table class="excel_tab" border="1" style="border-collapse: collapse">
				<tr>
					<td width="50">'.$item_number.'</td>		
					<td width="150">'.$artikul.'</td>
					<td width="300">'.$title.'</td>
					<td width="200" bgcolor="#FF0000"><font color="#FFFFFF">Изображение <b>'.$photo_prev.'</b> не найдено</font></td>
				</tr>
			</table>	
			';
		}
		// -- / если изображение не найдено / --
	}
	else
	{
		$warning = 
		'
		<table class="excel_tab" border="1" style="border-collapse: collapse">
			<tr>
				<td width="50">'.$item_number.'</td>		
				<td width="150">'.$artikul.'</td>
				<td width="300">'.$title.'</td>
				<td width="200" bgcolor="#9966FF"><font color="#FFFFFF"><b>Изображение не указано в прайсе</b></font></td>
			</tr>
		</table>	
		';
	}
	// --- / если изображение указано в прайс-листе / ---		
}



// ======= ЗАГРУЗКА ИЗОБРАЖЕНИЯ ================================================================

$tmp_file = $pt.'/components/shop/photo/temp_file.jpg';

if(file_exists($imagefile_tmp))
{
	Copy($imagefile_tmp,$tmp_file);
	img_load($imagefile_name,$tmp_file);
}
else {$photo = ""; $photobig = "";}	
	
		
// Обновляем данные в таблице "com_shop_item" 
$shop_item_update_query = "UPDATE `com_shop_item` SET `photo` = '$photo', `photobig` = '$photobig', `cdate` = NOW() WHERE `id` = '$item_id' ;";
			
$shop_item_sql = mysql_query($shop_item_update_query) or die ("Невозможно обновить данные");	

// ======= / загрузка изображения =================================================================



// ======= ВЫВОДИМ ОТВЕТ ========================================================================== 

$item_pct = intval(100*$item_number/$item_sum);

if ($item_number >0) // перопускаем 0 ход.
{
	echo 
	'
	<table border="0" cellpadding="0" style="border-collapse: collapse">
		<tr>
			<td width="50">&nbsp;</td>
			<td width="10">&nbsp;</td>
			<td width="200"><div align="center">'.$item_pct.' %</div></td>
			<td width="10">&nbsp;</td>
			<td width="50">&nbsp;</td>
		</tr>
		<tr>
			<td><div align="right">'.$item_number.'</div></td>
			<td>&nbsp;</td>
			<td class="loading-gray"><img height="20" width="'.$item_pct.'%" src="http://'.$site.'/components/shop/admin/tmp/images/loading_green.png" /></td>
			<td>&nbsp;</td>
			<td><div id="item_sum">'.$item_sum.'</div></td>
		</tr>
	</table>
	<div>&nbsp;</div>
	<div>Обрабатываем:</div>
	<div id="message_loading">
		<table class="excel_tab" border="1" style="border-collapse: collapse">
			<tr>
				<td width="50" class="excel_tab_hc">№</td>	
				<td width="150" class="excel_tab_hc">Артикул</td>
				<td width="300" class="excel_tab_hc">Наименование</td>
				<td width="200" class="excel_tab_hc">Изображение</td>		
			</tr>
		</table>
		<table class="excel_tab" border="1" style="border-collapse: collapse">
			<tr>
				<td width="50">'.$item_number.'</td>
				<td width="150">'.$artikul.'</td>
				<td width="300">'.$title.'</td>
				<td width="200">'.$photo_prev.'</td>	
			</tr>
		</table>
	</div>
	<div>&nbsp;</div>
	<div id="message_warning">'.$warning.'</div>
	';
}
else {echo '<div id="message_warning"></div>';}
// ======= / выводим ответ ===================================================================== 



// ####### ФУНКЦИИ #############################################################################
// === ФУНКЦИЯ РЕСАЙЗА И ЗАГРУЗКИ ИЗОБРАЖЕНИЯ ==================================================

function img_load($imagefile_name, $tmp_file)
{
	global $pt, $photo, $photobig, $warning, $item_number, $artikul, $title;
	
	// подключение настроек
	$shop_setting_sql = mysql_query("SELECT * FROM `com_shop_settings`") or die ("Невозможно сделать выборку из таблицы - 1");

	while($m = mysql_fetch_array($shop_setting_sql)):
		$setting_id = $m['id'];
		$setting_name = $m['name'];
		$setting_parametr = $m['parametr'];
		
		// размер по "х" малого изображения 
		if ($setting_name == "x_small")
		{
			$x_small = $setting_parametr;
		} 	
				
		// размер по "y" малого изображения 
		if ($setting_name == "y_small")
		{
			$y_small = $setting_parametr;
		}
		
		// размер по "х" большого изображения 
		if ($setting_name == "x_big")
		{
			$x_big = $setting_parametr;
		} 	
				
		// размер по "y" большого изображения 
		if ($setting_name == "y_big")
		{
			$y_big = $setting_parametr;
		}		
		
		// метод ресайза 
		if ($setting_name == "small_resize_method")
		{
			$small_resize_method = $setting_parametr;
		} 
		
	endwhile;	
	
	$imagefile_name_lower = strtolower($imagefile_name); // переводим в нижний регистр

	if(preg_match("/\.jpg|\.jpeg/",$imagefile_name_lower))
	{
		$cop = imagecreatefromJpeg($tmp_file);
	}
	elseif(preg_match("/\.png/",$imagefile_name_lower))
	{
		$cop = imagecreatefrompng($tmp_file);
	}
	elseif(preg_match("/\.gif/",$imagefile_name_lower))
	{
		$cop = imagecreatefromgif($tmp_file);
	}
	else
	{
		$err_photo = 1;
	}
	
	// если не удалось обработать изображение
	if (!$cop || $err_photo == 1)
	{
	//если залито что то не то, то он пошлёт нафиг и удалит залитое
	
	@chmod($tmp_file,0755);
	unlink($tmp_file);
	$warning = 
	'
	<table class="excel_tab" border="1" style="border-collapse: collapse">
		<tr>
			<td width="50">'.$item_number.'</td>		
			<td width="150">'.$artikul.'</td>
			<td width="300">'.$title.'</td>
			<td width="200" bgcolor="#FF00FF"><font color="#000000"><b>Не правильный формат<br/> изображения '.$imagefile_name.'</b></font></td>
		</tr>
	</table>	
	';
	}
	
	// --- Первоначальные настройки --------------------------------------------------------------
	$photo_dir = $pt.'/components/shop/photo/'; 
	$rand = rand(1000000,9999999);
	$file_name = date('ymdHis-').$rand."_.jpg";
	$file_name2 = date('ymdHis-').$rand.".jpg";
	
	// ------- Большая фотка ---------------------------------------------------------------------
	
	//посчитаем размеры картинки
	
	$size = getimagesize($tmp_file);
	
	// обработка изображения 
	
	$width=$size[0];
	$height=$size[1];
	
//	if ($height > 1000 || $width > 1000){echo "Превышен размер изображения"; exit;}	

	// пересчёт размера для большой фотки (умный ресайз)
	
	// $x_big = '600';
	// $y_big = '450';
	
	$x_ratio = $x_big / $width;
	$y_ratio = $y_big / $height;
	if ( ($width <= $x_big) && ($height <= $y_big) )
	{
		$tn_width = $width;
		$tn_height = $height;
	}
	else if (($x_ratio * $height) < $y_big)
	{
		$tn_height = $x_ratio * $height;
		$tn_width = $x_big;
	}
	else
	{
		$tn_width = $y_ratio * $width;
		$tn_height = $y_big;
	}
	
	// --- Пересчёт размера для маленького изображения ---------------------------------------------
	
	// умный ресайз
	
	if ($small_resize_method == "1")
	{
		
		$x_ratio2 = $x_small / $width;
		$y_ratio2 = $y_small / $height;
		
		// если "большое изображение" меньше "малого изображения"
		if ( ($width <= $x_smal) && ($height <= $y_small) )
		{
			$tn_width2 = $width;
			$tn_height2 = $height;
		}
		// находим меньшую сторону - высота меньше ширины (с учётом пропорциональности)
		else if (($x_ratio2 * $height) <= $y_small)
		{
			$tn_height2 = $x_ratio2 * $height;
			$tn_width2 = $x_small;
		}
		// ширина меньше высоты
		else
		{
			$tn_width2 = $y_ratio2 * $width;
			$tn_height2 = $y_small;
		}
		
		$src_x = 0;
		$src_y = 0;
		
		$width2 = $width;
		$height2 = $height;		
		
	} // умный ресайз
	
	// подрезка
	elseif ($small_resize_method == "2")
	{
		
		$x_ratio2 = $x_small / $width;
		$y_ratio2 = $y_small / $height;
		

		// находим меньшую сторону - высота меньше ширины (с учётом пропорциональности)
		if (($x_ratio2 * $height) <= $x_small)
		{	
			$width2 = $x_small / $y_ratio2;
			$height2 = $height;
			
			$src_x = ($width - $width2)/2;
			$src_y = 0;			
		}
		
		// ширина меньше высоты
		else
		{
			$height2 = $y_small / $x_ratio2;
			$width2 = $width;
			
			$src_x = 0;
			$src_y = ($height - $height2)/2;					
		}
		
		$tn_width2 = $x_small;
		$tn_height2 = $y_small;		

	}	
	
	// скукожить
	else 
	{
		$tn_width2 = $x_small;
		$tn_height2 = $y_small;	
		
		$src_x = 0;
		$src_y = 0;	
		
		$width2 = $width;
		$height2 = $height;
	}
	
	// --- теперь преобразуем его в новое изображение -------------------------------------------------
	
	$trumb = imagecreatetruecolor($tn_width, $tn_height);
	$white = imagecolorallocate($trumb, 255, 255, 255); // белый фон
	imagefilledrectangle($trumb, 0, 0, $tn_width-1, $tn_height-1, $white); // рисуем белый прямоугольник
	$image=imagecopyresampled($trumb, $cop, 0, 0, 0, 0, $tn_width, $tn_height, $width, $height);
	
	$trumb2 = imagecreatetruecolor($tn_width2, $tn_height2);
	$white2 = imagecolorallocate($trumb2, 255, 255, 255); // белый фон
	imagefilledrectangle($trumb2, 0, 0, $tn_width2-1, $tn_height2-1, $white2); 	// рисуем белый прямоугольник
	$image2 = imagecopyresampled($trumb2, $cop, 0, 0, $src_x, $src_y, $tn_width2, $tn_height2, $width2, $height2);
	
	// --- параметром (50) мы уменьшаем качество изображения.чтобы этого не делать поставьте "-1" -----
	
	ImageJpeg($trumb,$photo_dir.$file_name,100);
	ImageJpeg($trumb2,$photo_dir.$file_name2,100);
	
	//освобождаем память и удаляем временный файл
	
	ImageDestroy($trumb);
	ImageDestroy($cop);
	@chmod($filaneme,0755);
	unlink($tmp_file);
	
	$photo = $file_name2;
	$photobig = $file_name;	
	
	return $photo;
	return $photobig;	
}

// ======= / функция ресайза и загрузки изображения ========================================================



?>
