<?php
defined('AUTH') or die('Restricted access');

include_once $_SERVER['DOCUMENT_ROOT']."/db.php";
include_once $_SERVER['DOCUMENT_ROOT']."/classes/Excel.php";

/**/

$excelPriceName = $_SERVER['DOCUMENT_ROOT'].'/temp/excel/price_export.xlsx';
$excel = new Excel();
$excel->fixRow(1);
$excel->setRowHeight(18);


/* ЧИСТИМ ДИРЕКТОРИЮ */

if(is_file($excelPriceName))
	@unlink($excelPriceName);

/* СЧИТАЕМ ПОЗИЦИИ КОЛОНОК */

$numberOfSections = getMaxDepthSections();

$arrayColumns = array(
	array('name' => 'Идентификатор',			'width' => 20,		'color' => '6C3800',	'number' => 0),
	array('name' => 'Наименование', 		'width' => 30,		'color' => '6D6D6D',	'number' => 0),
	array('name' => 'Вводный текст',		'width' => 20,		'color' => '6D6D6D',	'number' => 0),
	array('name' => 'Детальное описание',	'width' => 20,		'color' => '6D6D6D',	'number' => 0),
	array('name' => 'Цена', 				'width' => 10,		'color' => 'CE0000',	'number' => 0),
	array('name' => 'Старая цена', 			'width' => 15,		'color' => 'CE0000',	'number' => 0),
	array('name' => 'Распродажа', 			'width' => 15,		'color' => 'CE0000',	'number' => 0),
	array('name' => 'Валюта', 				'width' => 10,		'color' => 'CE0000',	'number' => 0),
	array('name' => 'Новинка', 				'width' => 10,		'color' => '00801B',	'number' => 0),
	array('name' => 'Хит', 					'width' => 10,		'color' => '00801B',	'number' => 0),
	array('name' => 'Рейтинг', 				'width' => 10,		'color' => '00801B',	'number' => 0),
	array('name' => 'Изображение', 			'width' => 20,		'color' => '97008B',	'number' => 0),
	array('name' => 'Галерея', 				'width' => 20,		'color' => '97008B',	'number' => 0),
	array('name' => 'Действие', 			'width' => 10,		'color' => '5B5B5B',	'number' => 0),
	array('name' => 'Сопутствующие товары', 'width' => 25,		'color' => '008A13',	'number' => 0),
);

$i = $numberOfSections;
foreach($arrayColumns as &$iter)
	$iter['number'] = $i++;


/* ЗАГРУЖАЕМ ХАРАКТЕРИСТИКИ И ДОБАВЛЯЕМ КОЛОНКИ  */

$SQL = $db->query('SELECT name, unit, type FROM com_shop_char_name ORDER BY ordering');

foreach($SQL->fetchAll() as &$iter)
{
	$string = 'Характеристика;'.$iter['name'].';'.$iter['unit'].';';

	if($iter['type'] == 'string')
		$string .= 'строка';
	else
		$string .= 'число';

	if(array_key_exists('number', end($arrayColumns)))
		$number = ++end($arrayColumns)['number'];
	else
		continue;

	$arrayColumns[] = array(
			'name' => $string,
			'width' => 35,
			'color' => '730061',
			'number' => $number
		);
}


/* ЗАПОЛНЯЕМ ТАБЛИЦУ */

for($i = 0;$i < $numberOfSections;$i++)
	$excel->createCell($i, 1, 'Раздел', 20, '0084D1', 'ffffff');

foreach($arrayColumns as &$iter)
	$excel->createCell($iter['number'], 1, $iter['name'], $iter['width'], $iter['color'], 'ffffff');


/* СОХРАНЯЕМ ФАЙЛ */

$excel->save($excelPriceName);


/* ВОЗВРАЩАЕМ КОЛИЧЕСТВО ТОВАРОВ В ИНТЕРНЕТ _ МАГАЗИНЕ */

$SQL = $db->query('SELECT id, photo_big, photo_more FROM com_shop_item');
echo $SQL->rowCount();

/**/

exit();

function getMaxDepthSectionsRecursion($_id, &$_maxDepth, $_depth = 0)
{
	global $db;

	$_depth++;

	if($_depth > $_maxDepth)
		$_maxDepth = $_depth;

	$SQL_PREPARE = $db->prepare('SELECT id, parent FROM menu WHERE component = "shop" AND p1 = "section" AND parent = :parent');

	$SQL_PREPARE->execute(
			array(
				'parent' => $_id
				)
			);

	foreach($SQL_PREPARE->fetchAll() as $iter)
		getMaxDepthSectionsRecursion($iter['id'], $_maxDepth, $_depth);
}

function getMaxDepthSections()
{
	global $db;

	$maxDepth = 0;

	$SQL_PREPARE = $db->query('SELECT id FROM menu WHERE component = "shop" AND p1 = "section" AND parent = 0');
	$SQL_PREPARE->execute();

	foreach($SQL_PREPARE->fetchAll() as $iter)
		getMaxDepthSectionsRecursion($iter['id'], $maxDepth);

	return $maxDepth;
}
