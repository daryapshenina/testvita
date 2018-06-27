<?php
// DAN 2010
defined('AUTH') or die('Restricted access');

set_time_limit(900);
ini_set('memory_limit', '-1');

// ====== ПУТЬ К КЛАССАМ ==========================================================
set_include_path(get_include_path().PATH_SEPARATOR.'PhpExcel/Classes/');
// ====== путь к классам ==========================================================


// === ПОДКЛЮЧАЕМ PHPEXCEL ========================================================
include_once $_SERVER["DOCUMENT_ROOT"].'/classes/PHPExcel.php';

// Создаем класс
$pExcel = new PHPExcel();

// Индекс листа
$pExcel->setActiveSheetIndex(0);

// Даем ссылку на метод активного листа в который будем писать данные
$aSheet = $pExcel->getActiveSheet();

// Название активного листа
$aSheet->setTitle('Товары с сайта');
// === подключаем phpexcel ========================================================

// === ПИШЕМ ДАННЫЕ В ЛИСТ ========================================================
// Заголовки
$aSheet->setCellValue('A1','Раздел');
$aSheet->setCellValue('B1','Подраздел');
$aSheet->setCellValue('C1','Под-подраздел');
$aSheet->setCellValue('D1','Идентификатор товара');
$aSheet->setCellValue('E1','Наименование товара');
$aSheet->setCellValue('F1','Вводный текст');
$aSheet->setCellValue('G1','Детальное описание');
$aSheet->setCellValue('H1','Цена');
$aSheet->setCellValue('I1','Изображение');
$aSheet->setCellValue('J1','Действие');
$aSheet->setCellValue('K1','Характеристика 1');
$aSheet->setCellValue('L1','Характеристика 2');
$aSheet->setCellValue('M1','Характеристика 3');
$aSheet->setCellValue('N1','Характеристика 4');
$aSheet->setCellValue('O1','Характеристика 5');
$aSheet->setCellValue('P1','Характеристика 6');
$aSheet->setCellValue('Q1','Характеристика 7');
$aSheet->setCellValue('R1','Характеристика 8');
$aSheet->setCellValue('S1','Характеристика 9');
$aSheet->setCellValue('T1','Характеристика 10');
$aSheet->setCellValue('U1','Характеристика 11');
$aSheet->setCellValue('V1','Характеристика 12');
$aSheet->setCellValue('W1','Характеристика 13');
$aSheet->setCellValue('X1','Характеристика 14');
$aSheet->setCellValue('Y1','Характеристика 15');
$aSheet->setCellValue('Z1','Характеристика 16');
$aSheet->setCellValue('AA1','Характеристика 17');
$aSheet->setCellValue('AB1','Характеристика 18');
$aSheet->setCellValue('AC1','Характеристика 19');
$aSheet->setCellValue('AD1','Характеристика 20');
$aSheet->setCellValue('AE1','Характеристика 21');
$aSheet->setCellValue('AF1','Характеристика 22');
$aSheet->setCellValue('AG1','Характеристика 23');
$aSheet->setCellValue('AH1','Характеристика 24');
$aSheet->setCellValue('AI1','Характеристика 25');
$aSheet->setCellValue('AJ1','Характеристика 26');
$aSheet->setCellValue('AK1','Характеристика 27');
$aSheet->setCellValue('AL1','Характеристика 28');
$aSheet->setCellValue('AM1','Характеристика 29');
$aSheet->setCellValue('AN1','Характеристика 30');

// Номер ячеек, начинаем со второй
$c = 2;
$lvl_title_0 = $lvl_title_1 = $lvl_title_2 = $lvl_title_3 = '';

function tree($p, $lvl)
{
	global $aSheet, $c, $lvl_title_0, $lvl_title_1, $lvl_title_2;

	// Смотрим категории в меню начиная с родительских
	$numtree = mysql_query("SELECT * FROM `menu` WHERE `parent` = '$p'ORDER BY `ordering` ASC");

	// Смотрим есть такой пункт меню
	$result = mysql_num_rows($numtree);

	// Если да то продолжаем
	if ($result > 0)
	{
		while($m = mysql_fetch_array($numtree))
		{
			// Вынимаем данные
			$section_id = $m['id'];
			$section_id_com = $m['id_com'];
			$section_parent = $m['parent'];
			$section_title = $m['name'];
			$section_component = $m['component'];
			$section_p1 = $m['p1'];

			if ($lvl == 0)
			{
				$lvl_title_0 = $section_title;
				$lvl_title_1 = "";
				$lvl_title_2 = "";
			}
			elseif ($lvl == 1)
			{
				$lvl_title_1 = $section_title;
				$lvl_title_2 = "";
			}
			elseif ($lvl == 2)
			{
				$lvl_title_2 = $section_title;
			}
			
			$lvl++;
			
			// Если это раздел интернет-магазина то проверяем товары
			if ($section_component == 'shop' AND $section_p1 == 'section')
			{
				// Проверяем есть ли товары в данной категории
				$item = mysql_query("SELECT * FROM `com_shop_item` WHERE `section` = '$section_id_com'") or die ("Невозможно сделать выборку из таблицы - 1");
				
				// Смотрим есть ли товары
				$result_item = mysql_num_rows($item);

				// Если есть то перечисляем товары
				if ($result_item > 0)
				{
					while($si = mysql_fetch_array($item))
					{
						// Достаем данные о товарах
						$item_id = $si['id'];
						$item_identifier = $si['identifier'];
						$item_title = $si['title'];
						$item_pub = $si['pub'];
						$item_introtext = $si['introtext'];
						$item_fulltext = $si['fulltext'];
						$item_price = $si['price'];
						$item_photobig = $si['photobig'];
						
						// характеристики
						for($i = 1; $i < 31; $i++)
						{
							$characteristic[$i] = $si['characteristic_'.$i];;
						}
						
						// Если Идентификатор пустой то сначало генерируем его и записываем
						if ($item_identifier == '')
						{
							$item_identifier = substr((md5((date("F j, Y, g:i a").rand(0, 1000000)))), 0, 12);
							mysql_query("UPDATE `com_shop_item` SET `identifier` = '$item_identifier' WHERE `id` = '$item_id' LIMIT 1");
						}
						
						if ($item_pub == 0)
						{
							$item_pub_action = 'скрыть';
						}
						else
						{
							$item_pub_action = '';
						}

						// Пишем данные в таблицу
						$aSheet->setCellValue('A'.$c,$lvl_title_0);
						$aSheet->setCellValue('B'.$c,$lvl_title_1);
						$aSheet->setCellValue('C'.$c,$lvl_title_2);
						$aSheet->setCellValue('D'.$c,$item_identifier);
						$aSheet->setCellValue('E'.$c,$item_title);
						$aSheet->setCellValue('F'.$c,$item_introtext);
						$aSheet->setCellValue('G'.$c,$item_fulltext);
						$aSheet->setCellValue('H'.$c,$item_price);
						$aSheet->setCellValue('I'.$c,$item_photobig);
						$aSheet->setCellValue('J'.$c,$item_pub_action);
						$aSheet->setCellValue('K'.$c,$characteristic[1]);
						$aSheet->setCellValue('L'.$c,$characteristic[2]);
						$aSheet->setCellValue('M'.$c,$characteristic[3]);
						$aSheet->setCellValue('N'.$c,$characteristic[4]);
						$aSheet->setCellValue('O'.$c,$characteristic[5]);
						$aSheet->setCellValue('P'.$c,$characteristic[6]);
						$aSheet->setCellValue('Q'.$c,$characteristic[7]);
						$aSheet->setCellValue('R'.$c,$characteristic[8]);
						$aSheet->setCellValue('S'.$c,$characteristic[9]);
						$aSheet->setCellValue('T'.$c,$characteristic[10]);
						$aSheet->setCellValue('U'.$c,$characteristic[11]);
						$aSheet->setCellValue('V'.$c,$characteristic[12]);
						$aSheet->setCellValue('W'.$c,$characteristic[13]);
						$aSheet->setCellValue('X'.$c,$characteristic[14]);
						$aSheet->setCellValue('Y'.$c,$characteristic[15]);
						$aSheet->setCellValue('Z'.$c,$characteristic[16]);
						$aSheet->setCellValue('AA'.$c,$characteristic[17]);
						$aSheet->setCellValue('AB'.$c,$characteristic[18]);
						$aSheet->setCellValue('AC'.$c,$characteristic[19]);
						$aSheet->setCellValue('AD'.$c,$characteristic[20]);
						$aSheet->setCellValue('AE'.$c,$characteristic[21]);
						$aSheet->setCellValue('AF'.$c,$characteristic[22]);
						$aSheet->setCellValue('AG'.$c,$characteristic[23]);
						$aSheet->setCellValue('AH'.$c,$characteristic[24]);
						$aSheet->setCellValue('AI'.$c,$characteristic[25]);
						$aSheet->setCellValue('AJ'.$c,$characteristic[26]);
						$aSheet->setCellValue('AK'.$c,$characteristic[27]);
						$aSheet->setCellValue('AL'.$c,$characteristic[28]);
						$aSheet->setCellValue('AM'.$c,$characteristic[29]);
						$aSheet->setCellValue('AN'.$c,$characteristic[30]);
										
						// Увеличиваем значение номера ячейки только если были занесены данные
						$c++;
					}
				}
			}
			
			// Продолжаем рекурсию передав айди нашей категории для того что бы найти вложенную
			tree($section_id, $lvl);
			$lvl--;
		}
	}

} // функция - рекурсия

tree(0, 0);

// === пишем данные в лист ========================================================

// === ОФОРМЛЯЕМ ЛИСТ ========================================================
$aSheet->getColumnDimension('A')->setWidth(30);
$aSheet->getColumnDimension('B')->setWidth(30);
$aSheet->getColumnDimension('C')->setWidth(30);
$aSheet->getColumnDimension('D')->setWidth(20);
$aSheet->getColumnDimension('E')->setWidth(30);
$aSheet->getColumnDimension('F')->setWidth(30);
$aSheet->getColumnDimension('G')->setWidth(30);
$aSheet->getColumnDimension('H')->setWidth(10);
$aSheet->getColumnDimension('I')->setWidth(20);
$aSheet->getColumnDimension('J')->setWidth(10);
$aSheet->getColumnDimension('K')->setWidth(20);
$aSheet->getColumnDimension('L')->setWidth(20);
$aSheet->getColumnDimension('M')->setWidth(20);
$aSheet->getColumnDimension('N')->setWidth(20);
$aSheet->getColumnDimension('O')->setWidth(20);
$aSheet->getColumnDimension('P')->setWidth(20);
$aSheet->getColumnDimension('Q')->setWidth(20);
$aSheet->getColumnDimension('R')->setWidth(20);
$aSheet->getColumnDimension('S')->setWidth(20);
$aSheet->getColumnDimension('T')->setWidth(20);
$aSheet->getColumnDimension('U')->setWidth(20);
$aSheet->getColumnDimension('V')->setWidth(20);
$aSheet->getColumnDimension('W')->setWidth(20);
$aSheet->getColumnDimension('X')->setWidth(20);
$aSheet->getColumnDimension('Y')->setWidth(20);
$aSheet->getColumnDimension('Z')->setWidth(20);
$aSheet->getColumnDimension('AA')->setWidth(20);
$aSheet->getColumnDimension('AB')->setWidth(20);
$aSheet->getColumnDimension('AC')->setWidth(20);
$aSheet->getColumnDimension('AD')->setWidth(20);
$aSheet->getColumnDimension('AE')->setWidth(20);
$aSheet->getColumnDimension('AF')->setWidth(20);
$aSheet->getColumnDimension('AG')->setWidth(20);
$aSheet->getColumnDimension('AH')->setWidth(20);
$aSheet->getColumnDimension('AI')->setWidth(20);
$aSheet->getColumnDimension('AJ')->setWidth(20);
$aSheet->getColumnDimension('AK')->setWidth(20);
$aSheet->getColumnDimension('AL')->setWidth(20);
$aSheet->getColumnDimension('AM')->setWidth(20);
$aSheet->getColumnDimension('AN')->setWidth(20);

// === оформляем лист ========================================================


// === ОТДАЕМ ПОЛЬЗОВАТЕЛЮ ГОТОВЫЙ ЛИСТ ========================================================
include($_SERVER["DOCUMENT_ROOT"]."/classes/PHPExcel/Writer/Excel5.php");
$objWriter = new PHPExcel_Writer_Excel5($pExcel);
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment;filename="Прайс-лист товаров от '.date("j-m-Y в G часов i минут").'.xls"');
header('Cache-Control: max-age=0');
$objWriter->save('php://output');
// === отдаем пользователю готовый лист ========================================================

?>
