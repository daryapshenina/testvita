<?php
// DAN 2012
// Импорт данных из 1С
defined('AUTH') or die('Restricted access');

// время работы скрипта 90 сек.
set_time_limit(90); 


if ($_SESSION['ses_excel_1'] != 'process_1')
{	
	Header ("Location: http://".$site."/admin/com/shop/import"); exit;
}

$_SESSION['ses_excel_1'] = 'process_2'; 



$import_type = intval($_POST["import_type"]);

// ======= ПОЛУЧАМ EXCEL - ФАЙЛ ======================================================

// Оригинальное имя файла на компьютере клиента
$file_name = checkingeditor_2(@$_FILES['price']['name']);

// Размер в байтах принятого файла
$file_size = checkingeditor_2(@$_FILES['price']['size']);  

// Временное имя, с которым принятый файл был сохранен на сервере 
$tmp_name = @$_FILES['price']['tmp_name']; 

// Директория
$price_dir = 'components/shop/excel/'; 

// ЗАГРУЖАЕМ ФАЙЛ
file_upload($tmp_name, $file_name, $file_size, $price_dir);

// ======= / получаем excel - файл ===================================================


// Перенаправляем в зависимости от выбранного типа действий
// Если новая загрузка
if ($import_type == 1){include("components/shop/admin/import_excel_11.php");}
// Если обновление
if ($import_type == 2){include("components/shop/admin/import_excel_21.php");}


// ################################################################################################
// ======= ФУНКЦИЯ ЗАГРУЗКИ ФАЙЛА =================================================================

function file_upload($tmp_name, $file_name, $file_size, $price_dir)
{
	global $file_new, $file_name_arr;	
	// ------- ПРОВЕРКА РАЗМЕРА ФАЙЛА (НЕ БОЛЕЕ 10 МЕГАБАЙТ) -------
	if ($file_size >= 10000000) 
	{
		die ('
		<html>
		<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<title>Файл слишком большой!</title>
		</head>
		<body>
		<h3 align="center">Файл слишком большой! Максимальный размер файла не более 10 мегабайт.</h3>
		</body>
		</html>
		');
	}
	// ------- / проверка размера файла (не более 10 мегабайт) -------
	
	
	// ------- ПРОВЕРКА ТОГО, ЧТО ФАЙЛ ВЫБРАН (ПОЛЕ ОБЗОР НЕ ПУСТОЕ) -------
	if(!isset($file_name)|| $file_name=="" ) 
	{
		die ('
		<html>
		<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<title>Файл не выбран!</title>
		</head>
		<body>
		<h3 align="center">Файл не выбран!</h3>
		</body>
		</html>
		');	
	}
	// ------- / проверка того, что файл выбран (поле обзор не пустое) -------
	
	
	// Переводим в нижний регистр
	$file_name = strtolower($file_name); 

	// проверка на двойное разрешение 'test.php.zip'
	// разбиваем файл (оригинальное имя на компьютере клиента) на части по признаку '.' точка
	$file_name_arr = preg_split('/\./', $file_name, -1 , PREG_SPLIT_NO_EMPTY); 
	
	// размер массива; при значении > 2 означает, что у файла двойное расширение
	$count_tmp_arr = sizeof($file_name_arr);  
	
	// проверка на длинну расширения, находим длинну расширения
	$file_name_len = strlen ($file_name_arr[1]);
		
	// Проверяем запрещённые расширения; проверка двойного расширения; проверка на длинну расширения 
	if((preg_match("/(\.exe)|(\.php)|(\.cgi)|(\.pl)|(\.phtml)/", $file_name)) || $count_tmp_arr > 2 || $file_name_len > 4) // Проверка 
	{
		@chmod($tmp_name,0755);
		unlink($tmp_name);
		
		die ('
		<html>
		<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<title>Файл не формата "Excel" - расширение не "xls" или "xlsx"!</title>
		</head>
		<body>
		<h3 align="center">Файл не формата "Excel" - расширение не "xls" или "xlsx"!</h3>
		</body>
		</html>
		');				
	}
	else
	{
		// Проверяем разрешённые расширения	
		if(preg_match("/(\.xls)|(\.xlsx)/", $file_name)) 
		{
			// приклеиваем разрешение
			$file_name_new = 'price_upload'.'.'.$file_name_arr[1]; 
			
			$file_new = $price_dir.$file_name_new;

			// копируем временный файл в указанную директорию с указанным именем	
			move_uploaded_file($tmp_name, $file_new);
			@chmod($file_new,0644);
		}		
			
		else // удаляем временный файл
		{
			@chmod($tmp_name,0755);
			unlink($tmp_name);

			die ('
			<html>
			<head>
			<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
			<title>Файл не формата "Excel" - расширение не "xls" или "xlsx"!</title>
			</head>
			<body>
			<h3 align="center">Файл не формата "Excel" - расширение не "xls" или "xlsx"!</h3>
			</body>
			</html>
			');	
		}
	}
	
	return $file_new;
}


?>
