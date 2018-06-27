<?php
// DAN обновление - февраль 2014
defined('AUTH') or die('Restricted access');

// время работы скрипта 90 сек.
set_time_limit(90);

// отключаем отображение ошибок
ini_set('display_errors','Off'); 

session_start();

// не даём разорвать процесс; если процесс разорван - отправляем в начало процесса
if ($_SESSION['ses_excel_1'] != 'process_2')
{
	Header ("Location: http://".$site."/admin/com/shop/import"); exit;
}

$_SESSION['ses_excel_1'] = 'process_3'; 



// расширение экселевского файла
$ext = checkingeditor_2($_POST["ext"]);  

// Файл Excel на сервере для загрузки
$file_new = 'components/shop/excel/price_upload.'.$ext; 



function a_com()
{
	global $site, $root, $file_new;
	
	echo 
	'
	<table id="main-top-tab">
		<tr>
			<td class="imshop">Импорт данных из Excel - шаг 2 из 3</td>
			<td>&nbsp;</td>
		</tr>
	</table>
	<div class="margin-left-right-10">		
		<div>&nbsp;</div>		
		<form method="post" action="http://'.$site.'/admin/com/shop/import_and_export/import_excel_13" enctype="multipart/form-data">
			<input class="import_dalee" type="submit" value="Далее" name="bt">
			<input  type="hidden" value="'.$ext.'" name="ext">
		</form>
		<div>&nbsp;</div>
		<div class="import_excel">Данные из Excel загружены - <font color="#009933">шаг 2 из 3</font></div>
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
	global $site, $root, $ext;
	
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
					`char_enable_11`, `char_enable_12`, `char_enable_13`, `char_enable_14`, `char_enable_15`, `char_enable_16`, `char_enable_17`, `char_enable_18`, `char_enable_19`, `char_enable_20`,
					`char_enable_21`, `char_enable_22`, `char_enable_23`, `char_enable_24`, `char_enable_25`, `char_enable_26`, `char_enable_27`, `char_enable_28`, `char_enable_29`, `char_enable_30`,
					
					`characteristic_1`, `characteristic_2`, `characteristic_3`, `characteristic_4`, `characteristic_5`, `characteristic_6`, `characteristic_7`, `characteristic_8`, `characteristic_9`, `characteristic_10`,
					`characteristic_11`, `characteristic_12`, `characteristic_13`, `characteristic_14`, `characteristic_15`, `characteristic_16`, `characteristic_17`, `characteristic_18`, `characteristic_19`, `characteristic_20`,
					`characteristic_21`, `characteristic_22`, `characteristic_23`, `characteristic_24`, `characteristic_25`, `characteristic_26`, `characteristic_27`, `characteristic_28`, `characteristic_29`, `characteristic_30`,
					
					`char_unit_1`, `char_unit_2`, `char_unit_3`, `char_unit_4`, `char_unit_5`, `char_unit_6`, `char_unit_7`, `char_unit_8`, `char_unit_9`, `char_unit_10`,
					`char_unit_11`, `char_unit_12`, `char_unit_13`, `char_unit_14`, `char_unit_15`, `char_unit_16`, `char_unit_17`, `char_unit_18`, `char_unit_19`, `char_unit_20`,
					`char_unit_21`, `char_unit_22`, `char_unit_23`, `char_unit_24`, `char_unit_25`, `char_unit_26`, `char_unit_27`, `char_unit_28`, `char_unit_29`, `char_unit_30`,
					
					`filter_enable_1`, `filter_enable_2`, `filter_enable_3`, `filter_enable_4`, `filter_enable_5`, `filter_enable_6`, `filter_enable_7`, `filter_enable_8`, `filter_enable_9`, `filter_enable_10`,
					`filter_enable_11`, `filter_enable_12`, `filter_enable_13`, `filter_enable_14`, `filter_enable_15`, `filter_enable_16`, `filter_enable_17`, `filter_enable_18`, `filter_enable_19`, `filter_enable_20`,
					`filter_enable_21`, `filter_enable_22`, `filter_enable_23`, `filter_enable_24`, `filter_enable_25`, `filter_enable_26`, `filter_enable_27`, `filter_enable_28`, `filter_enable_29`, `filter_enable_30`,
					
					`filter_1`, `filter_2`, `filter_3`, `filter_4`, `filter_5`, `filter_6`, `filter_7`, `filter_8`, `filter_9`, `filter_10`,
					`filter_11`, `filter_12`, `filter_13`, `filter_14`, `filter_15`, `filter_16`, `filter_17`, `filter_18`, `filter_19`, `filter_20`,
					`filter_21`, `filter_22`, `filter_23`, `filter_24`, `filter_25`, `filter_26`, `filter_27`, `filter_28`, `filter_29`, `filter_30`,
					
					`date`
					) 
					VALUES (NULL, '$auto_increment', '$pub', '0', '$i', '$cel_value[0]', '', '$cel_value[0]', '', 
					'0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', 
					'', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', 
					'', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '',
					'0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0',
					'', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '',
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
				$section_test_1 = mysql_query("SELECT * FROM `com_shop_section` WHERE `title` = '$cel_value[1]' AND `parent` = '$section_id_0' LIMIT 1") or die ("Невозможно сделать выборку из таблицы - 10");
				
				$section_test_result_1 = mysql_num_rows($section_test_1);
				
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
					`char_enable_11`, `char_enable_12`, `char_enable_13`, `char_enable_14`, `char_enable_15`, `char_enable_16`, `char_enable_17`, `char_enable_18`, `char_enable_19`, `char_enable_20`,
					`char_enable_21`, `char_enable_22`, `char_enable_23`, `char_enable_24`, `char_enable_25`, `char_enable_26`, `char_enable_27`, `char_enable_28`, `char_enable_29`, `char_enable_30`,
					
					`characteristic_1`, `characteristic_2`, `characteristic_3`, `characteristic_4`, `characteristic_5`, `characteristic_6`, `characteristic_7`, `characteristic_8`, `characteristic_9`, `characteristic_10`,
					`characteristic_11`, `characteristic_12`, `characteristic_13`, `characteristic_14`, `characteristic_15`, `characteristic_16`, `characteristic_17`, `characteristic_18`, `characteristic_19`, `characteristic_20`,
					`characteristic_21`, `characteristic_22`, `characteristic_23`, `characteristic_24`, `characteristic_25`, `characteristic_26`, `characteristic_27`, `characteristic_28`, `characteristic_29`, `characteristic_30`,
					
					`char_unit_1`, `char_unit_2`, `char_unit_3`, `char_unit_4`, `char_unit_5`, `char_unit_6`, `char_unit_7`, `char_unit_8`, `char_unit_9`, `char_unit_10`,
					`char_unit_11`, `char_unit_12`, `char_unit_13`, `char_unit_14`, `char_unit_15`, `char_unit_16`, `char_unit_17`, `char_unit_18`, `char_unit_19`, `char_unit_20`,
					`char_unit_21`, `char_unit_22`, `char_unit_23`, `char_unit_24`, `char_unit_25`, `char_unit_26`, `char_unit_27`, `char_unit_28`, `char_unit_29`, `char_unit_30`,
					
					`filter_enable_1`, `filter_enable_2`, `filter_enable_3`, `filter_enable_4`, `filter_enable_5`, `filter_enable_6`, `filter_enable_7`, `filter_enable_8`, `filter_enable_9`, `filter_enable_10`,
					`filter_enable_11`, `filter_enable_12`, `filter_enable_13`, `filter_enable_14`, `filter_enable_15`, `filter_enable_16`, `filter_enable_17`, `filter_enable_18`, `filter_enable_19`, `filter_enable_20`,
					`filter_enable_21`, `filter_enable_22`, `filter_enable_23`, `filter_enable_24`, `filter_enable_25`, `filter_enable_26`, `filter_enable_27`, `filter_enable_28`, `filter_enable_29`, `filter_enable_30`,
					
					`filter_1`, `filter_2`, `filter_3`, `filter_4`, `filter_5`, `filter_6`, `filter_7`, `filter_8`, `filter_9`, `filter_10`,
					`filter_11`, `filter_12`, `filter_13`, `filter_14`, `filter_15`, `filter_16`, `filter_17`, `filter_18`, `filter_19`, `filter_20`,
					`filter_21`, `filter_22`, `filter_23`, `filter_24`, `filter_25`, `filter_26`, `filter_27`, `filter_28`, `filter_29`, `filter_30`,
					
					`date`
					) 
					VALUES (NULL, '$auto_increment', '$pub', '$section_id_0', '$i', '$cel_value[1]', '', '$cel_value[1]', '', 
					'0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', 
					'', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', 
					'', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '',
					'0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0',
					'', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '',
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
					while($s1 = mysql_fetch_array($section_test_1)):
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
					`char_enable_11`, `char_enable_12`, `char_enable_13`, `char_enable_14`, `char_enable_15`, `char_enable_16`, `char_enable_17`, `char_enable_18`, `char_enable_19`, `char_enable_20`,
					`char_enable_21`, `char_enable_22`, `char_enable_23`, `char_enable_24`, `char_enable_25`, `char_enable_26`, `char_enable_27`, `char_enable_28`, `char_enable_29`, `char_enable_30`,
					
					`characteristic_1`, `characteristic_2`, `characteristic_3`, `characteristic_4`, `characteristic_5`, `characteristic_6`, `characteristic_7`, `characteristic_8`, `characteristic_9`, `characteristic_10`,
					`characteristic_11`, `characteristic_12`, `characteristic_13`, `characteristic_14`, `characteristic_15`, `characteristic_16`, `characteristic_17`, `characteristic_18`, `characteristic_19`, `characteristic_20`,
					`characteristic_21`, `characteristic_22`, `characteristic_23`, `characteristic_24`, `characteristic_25`, `characteristic_26`, `characteristic_27`, `characteristic_28`, `characteristic_29`, `characteristic_30`,
					
					`char_unit_1`, `char_unit_2`, `char_unit_3`, `char_unit_4`, `char_unit_5`, `char_unit_6`, `char_unit_7`, `char_unit_8`, `char_unit_9`, `char_unit_10`,
					`char_unit_11`, `char_unit_12`, `char_unit_13`, `char_unit_14`, `char_unit_15`, `char_unit_16`, `char_unit_17`, `char_unit_18`, `char_unit_19`, `char_unit_20`,
					`char_unit_21`, `char_unit_22`, `char_unit_23`, `char_unit_24`, `char_unit_25`, `char_unit_26`, `char_unit_27`, `char_unit_28`, `char_unit_29`, `char_unit_30`,
					
					`filter_enable_1`, `filter_enable_2`, `filter_enable_3`, `filter_enable_4`, `filter_enable_5`, `filter_enable_6`, `filter_enable_7`, `filter_enable_8`, `filter_enable_9`, `filter_enable_10`,
					`filter_enable_11`, `filter_enable_12`, `filter_enable_13`, `filter_enable_14`, `filter_enable_15`, `filter_enable_16`, `filter_enable_17`, `filter_enable_18`, `filter_enable_19`, `filter_enable_20`,
					`filter_enable_21`, `filter_enable_22`, `filter_enable_23`, `filter_enable_24`, `filter_enable_25`, `filter_enable_26`, `filter_enable_27`, `filter_enable_28`, `filter_enable_29`, `filter_enable_30`,
					
					`filter_1`, `filter_2`, `filter_3`, `filter_4`, `filter_5`, `filter_6`, `filter_7`, `filter_8`, `filter_9`, `filter_10`,
					`filter_11`, `filter_12`, `filter_13`, `filter_14`, `filter_15`, `filter_16`, `filter_17`, `filter_18`, `filter_19`, `filter_20`,
					`filter_21`, `filter_22`, `filter_23`, `filter_24`, `filter_25`, `filter_26`, `filter_27`, `filter_28`, `filter_29`, `filter_30`,
					
					date
					) 
					VALUES (NULL, '$auto_increment', '$pub', '$section_id_1', '$i', '$cel_value[2]', '', '$cel_value[2]', '', 
					'0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', 
					'', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', 
					'', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '',
					'0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0',
					'', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '',
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
			
			// вставляем товар, если хотя бы поле "Наименование" заполнено 
			if (isset($cel_value[4]) && $cel_value[4] != "" )
			{
				// проверяем уникальность Идентификатора
				$item_query = mysql_query("SELECT * FROM `com_shop_item` WHERE `identifier` = '$cel_value[3]' LIMIT 1") or die ("Невозможно сделать выборку из таблицы - 18");
				
				$item__result = mysql_num_rows($item_query);
				// если Идентификатор не уникальный
				if($item__result > 0)
				{
					echo '<tr>';			
						echo '<td bgcolor="#ff0000" height="20">'.$i.'</td>';	
						echo '<td bgcolor="#ff0000"><font color="#ffffff"><b>ИДЕНТИФИКАТОР ТОВАРА НЕ УНИКАЛЬНЫЙ!</b></font></td>';	
						echo '<td bgcolor="#ff0000">'.$cel_value[1].'</td>';
						echo '<td bgcolor="#ff0000">'.$cel_value[2].'</td>';
						echo '<td bgcolor="#ff0000">'.$cel_value[3].'</td>';
						echo '<td bgcolor="#ff0000">'.$cel_value[4].'</td>';
						echo '<td bgcolor="#ff0000">'.$cel_value[5].'</td>';
						echo '<td bgcolor="#ff0000">'.$cel_value[6].'</td>';
						echo '<td bgcolor="#ff0000">'.$cel_value[7].'</td>';
						echo '<td bgcolor="#ff0000">'.$photobig.'</td>';	
						echo '<td bgcolor="#ff0000">'.$cel_value[9].'</td>';						
					echo '</tr>';					
				}
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
						
						// если у ячейки стоит признак "удалить"
						if($item_delete == 1)
						{
							$bgcolor[3]	= 'bgcolor="#cccccc"';
							$bgcolor[4]	= 'bgcolor="#cccccc"';	
							$bgcolor[5]	= 'bgcolor="#cccccc"';	
							$bgcolor[6]	= 'bgcolor="#cccccc"';	
							$bgcolor[7]	= 'bgcolor="#cccccc"';
							$bgcolor[8]	= 'bgcolor="#cccccc"';	
							$bgcolor[9]	= 'bgcolor="#cccccc"';
						}

					$tag_description = $cel_value[4].'. Цена: '.$price.' руб.';
					
					// переводим в floatval
					$cel_value_35 = floatval($cel_value[35]);
					$cel_value_36 = floatval($cel_value[36]);
					$cel_value_37 = floatval($cel_value[37]);
					$cel_value_38 = floatval($cel_value[38]);
					$cel_value_39 = floatval($cel_value[39]);
					
					// Вставляем в таблицу "com_shop_item"	нулевая дата признак того, что данные внесены из Excel<br />
					// После обновления фото - дата ставиться актуальной
					$item_insert_sql = "INSERT INTO `com_shop_item` (`id`, `identifier`, `section`, `pub`, `parent`, `ordering`, `title`, `introtext`, `fulltext`, `etext_enabled`, `etext`, `price`, `priceold`, `quantity`, `photo`, `photobig`, `photomore`, `new`, `discount`, `cdate`, `tag_title`, `tag_description`, `characteristic_1`, `characteristic_2`, `characteristic_3`, `characteristic_4`, `characteristic_5`, `characteristic_6`, `characteristic_7`, `characteristic_8`, `characteristic_9`, `characteristic_10`,
						`characteristic_11`, `characteristic_12`, `characteristic_13`, `characteristic_14`, `characteristic_15`, `characteristic_16`, `characteristic_17`, `characteristic_18`, `characteristic_19`, `characteristic_20`,
						`characteristic_21`, `characteristic_22`, `characteristic_23`, `characteristic_24`, `characteristic_25`, `characteristic_26`, `characteristic_27`, `characteristic_28`, `characteristic_29`, `characteristic_30`) VALUES (NULL, '$cel_value[3]','$section', '$pub', '0', '$i', '$cel_value[4]', '$cel_value[5]', '$cel_value[6]', '0', '', '$price', '0', '1.00', '$photobig', '', '', '', '', '0000-00-00 00:00:00', '$cel_value[4]', '$tag_description', '$cel_value[10]', '$cel_value[11]', '$cel_value[12]', '$cel_value[13]', '$cel_value[14]', '$cel_value[15]', '$cel_value[16]', '$cel_value[17]', '$cel_value[18]', '$cel_value[19]',
						'$cel_value[20]', '$cel_value[21]', '$cel_value[22]', '$cel_value[23]', '$cel_value[24]', '$cel_value[25]', '$cel_value[26]', '$cel_value[27]', '$cel_value[28]', '$cel_value[29]',
						'$cel_value[30]', '$cel_value[31]', '$cel_value[32]', '$cel_value[33]', '$cel_value[34]', '$cel_value_35', '$cel_value_36', '$cel_value_37', '$cel_value_38', '$cel_value_39')";
						
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
			}		
			// ------- / товары -------
		}
			
		$i++;
	}
	echo '</table>';
}

?>
