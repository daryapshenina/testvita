<?php
// DAN обновление - февраль 2014
defined('AUTH') or die('Restricted access');

// время работы скрипта 90 сек.
set_time_limit(90);

// отключаем отображение ошибок
// ini_set('display_errors','Off'); 

session_start();

// не даём разорвать процесс; если процесс разорван - отправляем в начало процесса
if ($_SESSION['ses_excel_1'] != 'process_2')
{
	Header ("Location: http://".$site."/admin/com/shop/import"); exit;
}

$_SESSION['ses_excel_1'] = 'process_3'; 



// расширение экселевского файла
$ext = checkingeditor_2($_POST["ext"]);  

// опции обновления
$introtext_update = intval($_POST['intro_text']);
$fulltext_update = intval($_POST['full_text']);
$image_update = intval($_POST['image']);

// Файл Excel на сервере для загрузки
$file_new = 'components/shop/excel/price_upload.'.$ext; 



function a_com()
{
	global $site, $root, $file_new, $introtext_update, $fulltext_update, $image_update;
	
	if ($image_update == 0)
	{
		$step = 2;
		$next = '
		<div>Обновление завершено</div>
		<div>&nbsp;</div>
		<div class="import_excel">Данные из Excel загружены - <font color="#009933">шаг 2 из 2</font></div>
		';
	}
	else 
	{
		$step = 3;
		$next = '
		<form method="post" action="http://'.$site.'/admin/com/shop/import_and_export/import_excel_13" enctype="multipart/form-data">
			<input class="import_dalee" type="submit" value="Далее" name="bt">
			<input  type="hidden" value="'.$ext.'" name="ext">
		</form>
		<div>&nbsp;</div>
		<div class="import_excel">Данные из Excel загружены - <font color="#009933">шаг 2 из 3</font></div>
		';		
	}
	
	echo 
	'
	<table id="main-top-tab">
		<tr>
			<td class="imshop">Импорт данных из Excel - шаг 2 из '.$step.'</td>
			<td>&nbsp;</td>
		</tr>
	</table>
	<div class="margin-left-right-10">		
		<div>&nbsp;</div>		
		'.$next.'
		<div>дождитесь полной загрузки страницы</div>
		<div>&nbsp;</div>		
		<hr/>
		<div>Цветовая маркировка:</div>	
		<div>&nbsp;</div>
		<table class="excel_tab" border="1" style="border-collapse: collapse">
			<tr>
				<td height="20" width="100"><b>Цвет</b></td>
				<td width="300"><b>Значение</b></td>
			</tr>
			<tr>
				<td height="20"  bgcolor="#FFFF00">&nbsp;</td>
				<td width="300">Заголовок таблицы (не учавствует в обработке)</td>
			</tr>
			<tr>
				<td height="20" bgcolor="#C8FF99">&nbsp;</td>
				<td width="300">Данные загружены заново</td>
			</tr>
			<tr>
				<td height="20" bgcolor="#C8F0FF">&nbsp;</td>
				<td width="300">Данные обновлены</td>
			</tr>
			<tr>
				<td height="20" bgcolor="#cccccc">&nbsp;</td>
				<td width="300">Данные удалены</td>
			</tr>	
			<tr>
				<td height="20" bgcolor="#FF0000">&nbsp;</td>
				<td width="300">Ошибка обработки данных</td>
			</tr>	
			<tr>
				<td height="20">&nbsp;</td>
				<td width="300">Данные не изменены</td>
			</tr>				
		</table>
		<div>&nbsp;</div>
		<div><b>Данные загружены:</b></div>	
		<div>&nbsp;</div>		
	';
	
	// ФУНКЦИЯ ОБРАБОТКИ EXCEL ФАЙЛА
	excel_to_db($file_new);	
		
	echo 
	'
		<div>&nbsp;</div>		
	</div>	
	';	
}


// ################################################################################################
// ======= ФУНКЦИЯ ВНЕСЕНИЯ ДАННЫХ В БАЗУ ДАННЫХ ======================================================
function excel_to_db($file_new)
{
	global $site, $root, $ext, $introtext_update, $fulltext_update, $image_update;
	
	// Находим тип меню
	$menu_type_query = mysql_query("SELECT `menu_type` FROM `menu` WHERE `component` = 'shop' AND `main` = '1' LIMIT 1") or die ("Невозможно сделать выборку из таблицы - 1");
	
	// тип меню
	$menu_type = mysql_result($menu_type_query, 0);
	
	// подключаем классы
	set_include_path(get_include_path().PATH_SEPARATOR.$root.'/classes/');
	
	// подключаем PHPExcel
	include $root.'/classes/PHPExcel.php';	
	
	// загрузка файла в PHPExcel Object  
	$objPHPExcel = PHPExcel_IOFactory::load($file_new);

	// получить активный лист
	$objWorksheet = $objPHPExcel->getActiveSheet();

	echo '<table class="excel_tab" border="1" style="border-collapse: collapse">';
		
	$i = 0;
	// получим итегратор строк и пройдемся по нему циклом
	foreach ($objWorksheet->getRowIterator() as $row) 
	{
		// в начале каждого цикла обнуляем значения ячеек - уничтожаем массив
		unset($cel_value); 
		
		// интегратор ячеек
		$cellIterator = $row->getCellIterator();
		
		// установить интеграцию только по заполненным клеткам - отменить
		$cellIterator->setIterateOnlyExistingCells(false); 
		
		$j = 0;
		// цикл по ячейкам
		foreach ($cellIterator as $cell) 
		{
			// получаем значение
			$cel_value[$j] = $cell->getValue();
			// затираем одиночные каычки 
			$cel_value[$j] = str_replace ("'", "", $cel_value[$j]);
			$cel_value[$j] = str_replace ("`", "", $cel_value[$j]); 
			// преобразум спецсимволы в представление
			//$cel_value[$j] = htmlspecialchars($cel_value[$j]);		
			$j++;
		}			
		
		// --- СВОЙСТВА ПОЛЕЙ ДАННЫХ ---
		// переводим в нижний регистр
		//$cel_value[9] = strtolower($cel_value[9]);
		if ($cel_value[9] == "скрыть"){$pub = 0;} else {$pub = 1;}
		if ($cel_value[9] == "удалить"){$item_delete = 1;} else {$item_delete = 0;}			
		 // --- / свойства полей данных ---
		
		// если это начальная строка (шапка таблицы)
		if($i == 0)
		{	
			echo '<tr>';			
				echo '<td class="excel_tab_hc">'.$i.'</td>';	
				echo '<td class="excel_tab_hc">'.$cel_value[0].'</td>';	
				echo '<td class="excel_tab_hc">'.$cel_value[1].'</td>';
				echo '<td class="excel_tab_hc">'.$cel_value[2].'</td>';
				echo '<td class="excel_tab_hc">'.$cel_value[3].'</td>';
				echo '<td class="excel_tab_hc">'.$cel_value[4].'</td>';
				echo '<td class="excel_tab_hc">'.$cel_value[5].'</td>';
				echo '<td class="excel_tab_hc">'.$cel_value[6].'</td>';
				echo '<td class="excel_tab_hc">'.$cel_value[7].'</td>';
				echo '<td class="excel_tab_hc">'.$cel_value[8].'</td>';	
				echo '<td class="excel_tab_hc">'.$cel_value[9].'</td>';						
			echo '</tr>';		
		}
		else 
		{
			// ------- РАЗДЕЛЫ -------
			// --- РАЗДЕЛЫ ВЕРХНЕГО УРОВНЯ ---
			// начальные настройки цвета ячейки
			$bgcolor[0] = 'bgcolor="#ffffff"';
			
			// если ячейка не пустая
			if(isset($cel_value[0]) && $cel_value[0] != "")
			{
				
				// поиск в уже существующих разделах записи с таким же наименованием
				$section_test_0_query = "SELECT * FROM `com_shop_section` WHERE `title` = '$cel_value[0]' LIMIT 1";
				
				$section_test_0 = mysql_query($section_test_0_query) or die ("Невозможно сделать выборку из таблицы - 7");
				$section_test_result_0 = mysql_num_rows($section_test_0);
		
				// если такого раздела нет
				if ($section_test_result_0 < 1)
				{
					// Смотрим последний Auto_increment
					$section_auto_increment_query = mysql_query("SHOW TABLE STATUS LIKE 'com_shop_section'");
					$section_auto_increment_array = mysql_fetch_array($section_auto_increment_query);
					$auto_increment = $section_auto_increment_array['Auto_increment'];
				
					// Вставляем в таблицу "com_shop_section"	
					$section_insert_sql_0 = "INSERT INTO `com_shop_section` (`id`, `identifier`, `pub`, `parent`, `ordering`, `title`, `description`, `tag_title`, `tag_description`, 
					`char_enable_1`, `char_enable_2`, `char_enable_3`, `char_enable_4`, `char_enable_5`, `char_enable_6`, `char_enable_7`, `char_enable_8`, `char_enable_9`, `char_enable_10`, 
					`characteristic_1`, `characteristic_2`, `characteristic_3`, `characteristic_4`, `characteristic_5`, `characteristic_6`, `characteristic_7`, `characteristic_8`, `characteristic_9`, `characteristic_10`, 
					`char_unit_1`, `char_unit_2`, `char_unit_3`, `char_unit_4`, `char_unit_5`, `char_unit_6`, `char_unit_7`, `char_unit_8`, `char_unit_9`, `char_unit_10`,
					`filter_enable_1`, `filter_enable_2`, `filter_enable_3`, `filter_enable_4`, `filter_enable_5`, `filter_enable_6`, `filter_enable_7`, `filter_enable_8`, `filter_enable_9`, `filter_enable_10`,
					`filter_1`, `filter_2`, `filter_3`, `filter_4`, `filter_5`, `filter_6`, `filter_7`, `filter_8`, `filter_9`, `filter_10`,
					date
					) 
					VALUES (NULL, '$auto_increment', '$pub', '0', '$i', '$cel_value[0]', '', '$cel_value[0]', '', 
					'0', '0', '0', '0', '0', '0', '0', '0', '0', '0', 
					'', '', '', '', '', '', '', '', '', '', 
					'', '', '', '', '', '', '', '', '', '',
					'0', '0', '0', '0', '0', '0', '0', '0', '0', '0',
					'', '', '', '', '', '', '', '', '', '',
					NOW()
					)";	
		
					$section_insert_query_0 = mysql_query($section_insert_sql_0) or die ("Невозможно сделать вставку - 8");
					
					$section_id_0 = mysql_insert_id();
					
					// Вставляем новый пункт в таблицу меню
					$query_insert_menu_0 = "INSERT INTO `menu` (menu_type, name, description, pub, parent, ordering, component, main, p1, p2, p3, id_com, prefix_css) VALUES('$menu_type', '$cel_value[0]', 'раздел интернет-магазина', '$pub', '0', '$i', 'shop', '0', 'section', '', '', '$section_id_0', '')";
		
					$sql_menu_0 = mysql_query($query_insert_menu_0) or die ("Невозможно вставить данные 9");	
						
					// фон ячеек
					$bgcolor[0]	= 'bgcolor="#c8ff99"';					
				}
				// если такой раздел есть
				else 
				{
					while($s0 = mysql_fetch_array($section_test_0)):
						$section_id_0 = $s0['id'];
					endwhile;						
				}
			}
			// --- / разделы верхнего уровня ---	
			
				
				
			// --- ПОДРАЗДЕЛ ---
			$bgcolor[1] = 'bgcolor="#ffffff"';
			
			// если ячейка не пустая				
			if(isset($cel_value[1]) && $cel_value[1] != "")
			{
				
				// поиск в уже существующих разделах записи с таким же наименованием
				$section_test_1_query = "SELECT * FROM `com_shop_section` WHERE `title` = '$cel_value[1]' AND `parent` = '$section_id_0' LIMIT 1";
				
				$section_test_1_sql = mysql_query($section_test_1_query) or die ("Невозможно сделать выборку из таблицы - 10");				
				
				$section_test_result_1 = mysql_num_rows($section_test_1_sql);
				
				// если такого раздела нет
				if ($section_test_result_1 < 1)
				{
					// Смотрим последний Auto_increment
					$section_auto_increment_query = mysql_query("SHOW TABLE STATUS LIKE 'com_shop_section'");
					$section_auto_increment_array = mysql_fetch_array($section_auto_increment_query);
					$auto_increment = $section_auto_increment_array['Auto_increment'];
				
					// вставляем в таблицу "com_shop_section"	
					$section_insert_sql_1 = "INSERT INTO `com_shop_section` (`id`, `identifier`, `pub`, `parent`, `ordering`, `title`, `description`, `tag_title`, `tag_description`, 
					`char_enable_1`, `char_enable_2`, `char_enable_3`, `char_enable_4`, `char_enable_5`, `char_enable_6`, `char_enable_7`, `char_enable_8`, `char_enable_9`, `char_enable_10`, 
					`characteristic_1`, `characteristic_2`, `characteristic_3`, `characteristic_4`, `characteristic_5`, `characteristic_6`, `characteristic_7`, `characteristic_8`, `characteristic_9`, `characteristic_10`, 
					`char_unit_1`, `char_unit_2`, `char_unit_3`, `char_unit_4`, `char_unit_5`, `char_unit_6`, `char_unit_7`, `char_unit_8`, `char_unit_9`, `char_unit_10`,
					`filter_enable_1`, `filter_enable_2`, `filter_enable_3`, `filter_enable_4`, `filter_enable_5`, `filter_enable_6`, `filter_enable_7`, `filter_enable_8`, `filter_enable_9`, `filter_enable_10`,
					`filter_1`, `filter_2`, `filter_3`, `filter_4`, `filter_5`, `filter_6`, `filter_7`, `filter_8`, `filter_9`, `filter_10`,
					date
					) 
					VALUES (NULL, '$auto_increment', '$pub', '$section_id_0', '$i', '$cel_value[1]', '', '$cel_value[1]', '', 
					'0', '0', '0', '0', '0', '0', '0', '0', '0', '0', 
					'', '', '', '', '', '', '', '', '', '', 
					'', '', '', '', '', '', '', '', '', '',
					'0', '0', '0', '0', '0', '0', '0', '0', '0', '0',
					'', '', '', '', '', '', '', '', '', '',
					NOW()
					)";
					
					$section_insert_query_1 = mysql_query($section_insert_sql_1) or die ("Невозможно сделать вставку в таблицу - 11");
						
					$section_id_1 = mysql_insert_id();
											
					// находим родительский раздел
					$parent_0_query = mysql_query("SELECT `id` FROM `menu` WHERE `component` = 'shop' AND `p1` = 'section' AND `id_com` = '$section_id_0' LIMIT 1") or die ("Невозможно сделать выборку из таблицы - 12");

					// id пункта меню родительского раздела
					$parent_0 = mysql_result($parent_0_query, 0);						
						
						
					// Вставляем новый пункт в таблицу меню
					$query_insert_menu_1 = "INSERT INTO `menu` (menu_type, name, description, pub, parent, ordering, component, main, p1, p2, p3, id_com, prefix_css) VALUES('$menu_type', '$cel_value[1]', 'раздел интернет-магазина', '$pub', '$parent_0', '$i', 'shop', '0', 'section', '', '', '$section_id_1', '')";
		
					$sql_menu_1 = mysql_query($query_insert_menu_1) or die ("Невозможно вставить данные 13");	
						
					// фон ячеек
					$bgcolor[1]	= 'bgcolor="#c8ff99"';	
				}
				// если такой раздел есть
				else 
				{
					while($s1 = mysql_fetch_array($section_test_1_sql)):
						$section_id_1 = $s1['id'];
					endwhile;						
				}
			}				
			// --- / подраздел
				
				
				
			// --- ПОД-ПОД-РАЗДЕЛ ---
			$bgcolor[2] = 'bgcolor="#ffffff"';
			
			// если ячейка не пустая				
			if(isset($cel_value[2]) && $cel_value[2] != "")
			{
				
				// поиск в уже существующих разделах записи с таким же наименованием
				$section_test_2 = mysql_query("SELECT * FROM `com_shop_section` WHERE `title` = '$cel_value[2]' AND `parent` = '$section_id_1' LIMIT 1") or die ("Невозможно сделать выборку из таблицы - 14");
					
				$section_test_result_2 = mysql_num_rows($section_test_2);
				
				// если такого раздела нет
				if ($section_test_result_2 < 1)
				{
					// Смотрим последний Auto_increment
					$section_auto_increment_query = mysql_query("SHOW TABLE STATUS LIKE 'com_shop_section'");
					$section_auto_increment_array = mysql_fetch_array($section_auto_increment_query);
					$auto_increment = $section_auto_increment_array['Auto_increment'];
				
					// вставляем в таблицу "com_shop_section"	
					$section_insert_sql_2 = "INSERT INTO `com_shop_section` (`id`, `identifier`, `pub`, `parent`, `ordering`, `title`, `description`, `tag_title`, `tag_description`, 
					`char_enable_1`, `char_enable_2`, `char_enable_3`, `char_enable_4`, `char_enable_5`, `char_enable_6`, `char_enable_7`, `char_enable_8`, `char_enable_9`, `char_enable_10`, 
					`characteristic_1`, `characteristic_2`, `characteristic_3`, `characteristic_4`, `characteristic_5`, `characteristic_6`, `characteristic_7`, `characteristic_8`, `characteristic_9`, `characteristic_10`, 
					`char_unit_1`, `char_unit_2`, `char_unit_3`, `char_unit_4`, `char_unit_5`, `char_unit_6`, `char_unit_7`, `char_unit_8`, `char_unit_9`, `char_unit_10`,
					`filter_enable_1`, `filter_enable_2`, `filter_enable_3`, `filter_enable_4`, `filter_enable_5`, `filter_enable_6`, `filter_enable_7`, `filter_enable_8`, `filter_enable_9`, `filter_enable_10`,
					`filter_1`, `filter_2`, `filter_3`, `filter_4`, `filter_5`, `filter_6`, `filter_7`, `filter_8`, `filter_9`, `filter_10`,
					date
					) 
					VALUES (NULL, '$auto_increment', '$pub', '$section_id_1', '$i', '$cel_value[2]', '', '$cel_value[2]', '', 
					'0', '0', '0', '0', '0', '0', '0', '0', '0', '0', 
					'', '', '', '', '', '', '', '', '', '', 
					'', '', '', '', '', '', '', '', '', '',
					'0', '0', '0', '0', '0', '0', '0', '0', '0', '0',
					'', '', '', '', '', '', '', '', '', '',
					NOW()
					)";							
						
					$section_insert_query_2 = mysql_query($section_insert_sql_2) or die ("Невозможно сделать вставку в таблицу - 15");
						
					$section_id_2 = mysql_insert_id();
						
						
					// находим родительский раздел
					$parent_1_query = mysql_query("SELECT `id` FROM `menu` WHERE `component` = 'shop' AND `p1` = 'section' AND `id_com` = '$section_id_1' LIMIT 1") or die ("Невозможно сделать выборку из таблицы - 16");
	
					// id пункта меню родительского раздела
					$parent_1 = mysql_result($parent_1_query, 0);							
						
					
					
					// Вставляем новый пункт в таблицу меню
					$query_insert_menu_2 = "INSERT INTO `menu` (menu_type, name, description, pub, parent, ordering, component, main, p1, p2, p3, id_com, prefix_css) VALUES('$menu_type', '$cel_value[2]', 'раздел интернет-магазина', '$pub', '$parent_1', '$i', 'shop', '0', 'section', '', '', '$section_id_2', '')";
		
					$sql_menu_2 = mysql_query($query_insert_menu_2) or die ("Невозможно вставить данные 17");	
						
					// фон ячеек						
					$bgcolor[2]	= 'bgcolor="#c8ff99"';							
				}
				// если такой раздел есть
				else 
				{
					while($s2 = mysql_fetch_array($section_test_2)):
						$section_id_2 = $s2['id'];
					endwhile;						
				}
			}				
			// --- / под-под-раздел				
			
			// ------- / разделы -------	





			// ------- ТОВАРЫ -------
			// Находим раздел для товара
			if (isset($cel_value[2]) && $cel_value[2] != ""){$section = $section_id_2;}
			else
			{
				if (isset($cel_value[1]) && $cel_value[1] != ""){$section = $section_id_1;}
				else {$section = $section_id_0;}
			}				
			
			$price = intval($cel_value[7]);
			
			// фон ячеек - по умолчанию						
			$bgcolor[3]	= '';
			$bgcolor[4]	= '';	
			$bgcolor[5]	= '';	
			$bgcolor[6]	= '';	
			$bgcolor[7]	= '';
			$bgcolor[8]	= '';	
			$bgcolor[9]	= '';
			
			// переводим в нижний регистр
			$photobig = mb_strtolower($cel_value[8], 'UTF-8');
			
			// Если хотя бы поле "Наименование" заполнено - действуем дальше
			if (isset($cel_value[4]) && $cel_value[4] != "" )
			{
				
				
				// --- УДАЛЯЕМ ТОВАР ---
				// если у ячейки стоит признак "удалить"
				if($item_delete == 1)
				{				
					// находим старые фотографии
					$item_delete_query = mysql_query("SELECT * FROM `com_shop_item` WHERE `identifier` = '$cel_value[3]' LIMIT 1") or die ("Невозможно сделать выборку из таблицы - 1");
					
					$item_delete_query_result = mysql_num_rows($item_delete_query);
					
					// --- ЕСЛИ ТОВАР ЕСТЬ В БАЗЕ - УДАЛЯЕМ ---
					if ($item_delete_query_result > 0)
					{
						while($n = mysql_fetch_array($item_delete_query)):
							$id = $n['id'];
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
						
						mysql_query("DELETE FROM `com_shop_item` WHERE `identifier` = '$cel_value[3]'");
					}
					// --- / если товар есть в базе - удаляем / ---

					echo '<tr>';			
						echo '<td height="20">'.$i.'</td>';	
						echo '<td '.$bgcolor[0].'>'.$cel_value[0].'</td>';	
						echo '<td '.$bgcolor[1].'>'.$cel_value[1].'</td>';
						echo '<td '.$bgcolor[2].'>'.$cel_value[2].'</td>';
						echo '<td bgcolor="#cccccc">'.$cel_value[3].'</td>';
						echo '<td bgcolor="#cccccc">'.$cel_value[4].'</td>';
						echo '<td bgcolor="#cccccc">'.$cel_value[5].'</td>';
						echo '<td bgcolor="#cccccc">'.$cel_value[6].'</td>';
						echo '<td bgcolor="#cccccc">'.$cel_value[7].'</td>';
						echo '<td bgcolor="#cccccc">'.$photobig.'</td>';	
						echo '<td bgcolor="#cccccc">'.$cel_value[9].'</td>';						
					echo '</tr>';						
				}
				// --- / удаляем товар / ---
				
				
				else
				{
					// проверяем уникальность Идентификатора
					$item_query = mysql_query("SELECT * FROM `com_shop_item` WHERE `identifier` = '$cel_value[3]' LIMIT 1") or die ("Невозможно сделать выборку из таблицы - 18");
					
					$item__result = mysql_num_rows($item_query);
					
					
					// --- ОБНОВЛЯЕМ ТОВАР ---
					// если Идентификатор не уникальный - обновляем товар
					if($item__result > 0)
					{	
						// находим старые фотографии
						$item_delete_query = mysql_query("SELECT * FROM `com_shop_item` WHERE `identifier` = '$cel_value[3]' LIMIT 1") or die ("Невозможно сделать выборку из таблицы - 1");
						
						while($n = mysql_fetch_array($item_delete_query)):
							$section = $n['section'];
							$photo_old_name = $n['photo']; 
							$photobig_old_name = $n['photobig']; 
						endwhile; 
						
						// удаляем старые фотографии
						$photo_dir = 'components/shop/photo/'; 
						$photo_old = $photo_dir.$photo_old_name;
						$photobig_old = $photo_dir.$photobig_old_name;
						
						// если стоит галочка обновлять изображения 
						if ($image_update == 1)
						{
							// если есть файл изображения и его имя не пустое - удяляем файлы изображения
							if (isset($photobig_old_name) && $photobig_old_name != "")
							{
								unlink($photo_old);			
								unlink($photobig_old);
							}
						}
						
						// --- ПРИЗНАКИ ВСТАВКИ ---
						// вводный текст 
						if ($introtext_update == 1)
						{
							$introtext = ' `introtext` = \''.$cel_value[5].'\',';
						}
						else {$introtext = '';}
						
						// полный текст
						if ($fulltext_update == 1)
						{
							$fulltext =  ' `fulltext` = \''.$cel_value[6].'\',';
						}
						else {$fulltext = '';}
						
						// изображение
						if ($image_update == 1)
						{	
							$image = ', `photo` = \''.$photobig.'\', `photobig` = \'\'';
							$cdate = ', `cdate` = \'0000-00-00 00:00:00\'';
						}
						else {$image = ''; $cdate = '';}
						// --- / признаки вставки /---
						
						// переводим в floatval
						$cel_value_17 = floatval($cel_value[17]);
						$cel_value_18 = floatval($cel_value[18]);
						$cel_value_19 = floatval($cel_value[19]);
						
						// Обновляем данные в таблице "com_shop_item"
						$shop_item_update_sql = "UPDATE `com_shop_item` SET pub = '$pub', `title` = '$cel_value[4]', ".$introtext.$fulltext." `price` = '$price' ".$image.$cdate.", `characteristic_1` = '$cel_value[10]', `characteristic_2` = '$cel_value[11]', `characteristic_3` = '$cel_value[12]', `characteristic_4` = '$cel_value[13]', `characteristic_5` = '$cel_value[14]', `characteristic_6` = '$cel_value[15]', `characteristic_7` = '$cel_value[16]', `characteristic_8` = '$cel_value_17', `characteristic_9` = '$cel_value_18', `characteristic_10` = '$cel_value_19'  WHERE `identifier` = '$cel_value[3]' LIMIT 1 ;";	
						
						
						
						
					
						
						
						
						$shop_item_update_query = mysql_query($shop_item_update_sql) or die ("Невозможно обновить данные 1");						
						
						echo '<tr>';			
							echo '<td height="20">'.$i.'</td>';	
							echo '<td '.$bgcolor[0].'>'.$cel_value[0].'</td>';	
							echo '<td '.$bgcolor[1].'>'.$cel_value[1].'</td>';
							echo '<td '.$bgcolor[2].'>'.$cel_value[2].'</td>';
							echo '<td bgcolor="#c8f0ff">'.$cel_value[3].'</td>';
							echo '<td bgcolor="#c8f0ff">'.$cel_value[4].'</td>';
							echo '<td bgcolor="#c8f0ff">'.$cel_value[5].'</td>';
							echo '<td bgcolor="#c8f0ff">'.$cel_value[6].'</td>';
							echo '<td bgcolor="#c8f0ff">'.$cel_value[7].'</td>';
							echo '<td bgcolor="#c8f0ff">'.$photobig.'</td>';	
							echo '<td bgcolor="#c8f0ff">'.$cel_value[9].'</td>';						
						echo '</tr>';					
					}
					// --- /обновляем товар / ---
					
					
					// --- ВСТАВЛЯЕМ ТОВАР ---
					else 
					{
						// фон ячеек для новых позиций - по умолчанию зелёный					
						$bgcolor[3]	= 'bgcolor="#c8ff99"';
						$bgcolor[4]	= 'bgcolor="#c8ff99"';	
						$bgcolor[5]	= 'bgcolor="#c8ff99"';	
						$bgcolor[6]	= 'bgcolor="#c8ff99"';	
						$bgcolor[7]	= 'bgcolor="#c8ff99"';
						$bgcolor[8]	= 'bgcolor="#c8ff99"';	
						$bgcolor[9]	= 'bgcolor="#c8ff99"';
						
						$tag_description = $cel_value[4].'. Цена: '.$price.' руб.';
						
						// переводим в floatval
						$cel_value_17 = floatval($cel_value[17]);
						$cel_value_18 = floatval($cel_value[18]);
						$cel_value_19 = floatval($cel_value[19]);
						
						// Вставляем в таблицу "com_shop_item"	нулевая дата признак того, что данные внесены из Excel<br />
						// После обновления фото - дата ставиться актуальной
						$item_insert_sql = "INSERT INTO `com_shop_item` (`id`, `identifier`, `section`, `pub`, `parent`, `ordering`, `title`, `introtext`, `fulltext`, `etext_enabled`, `etext`, `price`, `priceold`, `quantity`, `photo`, `photobig`, `photomore`, `new`, `discount`, `cdate`, `tag_title`, `tag_description`, `characteristic_1`, `characteristic_2`, `characteristic_3`, `characteristic_4`, `characteristic_5`, `characteristic_6`, `characteristic_7`, `characteristic_8`, `characteristic_9`, `characteristic_10`) VALUES (NULL, '$cel_value[3]','$section', '$pub', '0', '$i', '$cel_value[4]', '$cel_value[5]', '$cel_value[6]', '0', '', '$price', '1.00', '$photobig', '', '0000-00-00 00:00:00', '$cel_value[4]', '$tag_description', '$cel_value[10]', '$cel_value[11]', '$cel_value[12]', '$cel_value[13]', '$cel_value[14]', '$cel_value[15]', '$cel_value[16]', '$cel_value_17', '$cel_value_18', '$cel_value_19')";
						
						$item_insert_query = mysql_query($item_insert_sql) or die ("Невозможно обновить данные 19");
		
						echo '<tr>';			
							echo '<td height="20">'.$i.'</td>';	
							echo '<td '.$bgcolor[0].'>'.$cel_value[0].'</td>';	
							echo '<td '.$bgcolor[1].'>'.$cel_value[1].'</td>';
							echo '<td '.$bgcolor[2].'>'.$cel_value[2].'</td>';
							echo '<td '.$bgcolor[3].'>'.$cel_value[3].'</td>';
							echo '<td '.$bgcolor[4].'>'.$cel_value[4].'</td>';
							echo '<td '.$bgcolor[5].'>'.$cel_value[5].'</td>';
							echo '<td '.$bgcolor[6].'>'.$cel_value[6].'</td>';
							echo '<td '.$bgcolor[7].'>'.$cel_value[7].'</td>';
							echo '<td '.$bgcolor[8].'>'.$photobig.'</td>';	
							echo '<td '.$bgcolor[9].'>'.$cel_value[9].'</td>';						
						echo '</tr>';
					}	
					// --- / вставляем товар / ---
				}
				
				
				
				

			}		
			// ------- / товары -------
		}
			
		$i++;
	}
	echo '</table>';
}

?>
