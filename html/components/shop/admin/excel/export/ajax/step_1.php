<?php

const PATH_TO_PRICE = '/temp/excel/price_export.xlsx';

const EXCEL_CURRENCY_RUB = '0';
const EXCEL_CURRENCY_USD = '1';
const EXCEL_CURRENCY_EUR = '2';

include_once $_SERVER['DOCUMENT_ROOT']."/classes/Excel.php";
include_once $_SERVER['DOCUMENT_ROOT']."/components/shop/admin/classes/Items.php";
include_once $_SERVER['DOCUMENT_ROOT']."/components/shop/admin/classes/Sections.php";
include_once $_SERVER['DOCUMENT_ROOT']."/components/shop/admin/classes/RelatedItems.php";
include_once $_SERVER['DOCUMENT_ROOT']."/components/shop/classes/Chars.php";


/**/

$excel = new Excel($_SERVER['DOCUMENT_ROOT'].PATH_TO_PRICE);

$TIME_MAX = ini_get('max_execution_time') - 5;
$TIME_START = time();
$INDEX = (int)$_POST['index'];

/**/

$arrayColumns = array(
	'Идентификатор' => 0,
	'Наименование' => 0,
	'Вводный текст' => 0,
	'Детальное описание' => 0,
	'Цена' => 0,
	'Старая цена' => 0,
	'Распродажа' => 0,
	'Валюта' => 0,
	'Новинка' => 0,
	'Хит' => 0,
	'Рейтинг' => 0,
	'Изображение' => 0,
	'Галерея' => 0,
	'Действие' => 0,
	'Сопутствующие товары' => 0
);

$arrayColumnsChars = array(); /* key = charID, value = posX */


/* ЗАГРУЖАЕМ КОЛОНКИ */

for($x = 0; $x < $excel->getSizeX();$x++)
{
	$columnName = $excel->getCell($x, 0);

	if(array_key_exists($columnName, $arrayColumns))
	{
		$arrayColumns[$columnName] = $x;
	}
	else
	{
		$array = explode(';', $columnName);

		if(count($array) == 4 && $array[0] == 'Характеристика')
		{
			$name = $array[1];
			$unit = $array[2];
			$type = $array[3];

			if($type == 'строка')
				$type = 'string';
			else
				$type = 'number';

			$id = Chars::getNameID($name, $unit, $type);

			if($id > 0)
				$arrayColumnsChars[$id] = $x;
		}
	}
}


/* ЗАПОЛНЯЕМ ТАБЛИЦУ */

while(true)
{
	$SQL_PREPARE = $db->prepare('SELECT * FROM com_shop_item ORDER BY section LIMIT 1 OFFSET :offset');
	$SQL_PREPARE->bindValue(':offset', (int)$INDEX, PDO::PARAM_INT);
	$SQL_PREPARE->execute();
	$item = $SQL_PREPARE->fetchAll();

	/**/

	if(count($item) == 0)
	{
		$excel->save($_SERVER['DOCUMENT_ROOT'].PATH_TO_PRICE);
		exit('-1');
	}

	/**/

	$menuID = Shop\Sections\getSectionByID($item[0]['section'])[0]['id'];
	$sections = Shop\Sections\getSectionParentsByMenuID($menuID);

	$cellY = $INDEX + 2;

	/* Добавляем категории */

	for($i = 0;$i < count($sections);$i++)
		$excel->createCell($i, $cellY, $sections[$i]['name']);

	/* Добавляем информацию о товаре */

	$id			= $item[0]['id'];
	$identifier	= $item[0]['identifier'];
	$name		= $item[0]['title'];
	$intro_text	= $item[0]['intro_text'];
	$full_text	= $item[0]['full_text'];
	$price		= $item[0]['price'];
	$price_old	= $item[0]['price_old'];
	$discount	= $item[0]['discount'];
	$currency	= $item[0]['currency'];
	$new		= $item[0]['new'];
	$hit		= $item[0]['hit'];
	$rating		= $item[0]['rating'];
	$photo		= $item[0]['photo_big'];
	$photo_more	= $item[0]['photo_more'];
	$action		= $item[0]['pub'];

	/**/

	if(!$action)
		$action = 'скрыт';
	else
		$action = '';

	switch($currency)
	{
		case EXCEL_CURRENCY_USD:
			$currency = 'usd';
			break;

		case EXCEL_CURRENCY_EUR:
			$currency = 'eur';
			break;

		default:
			$currency = '';
	}

	if($new)
		$new = 'Да';
	else
		$new = '';

	if($hit)
		$hit = 'Да';
	else
		$hit = '';

	if($rating == 0)
		$rating = '';

	if($discount)
		$discount = 'Да';
	else
		$discount = '';

	$photo_more = str_replace('.jpg', '_.jpg', $photo_more);

	/* Генерация идентификатора, если пуст */

	if(strlen($identifier) == 0)
	{
		$identifier = (string)date('m.d.y h:i:s');
		$identifier .= (string)rand(0, 1000000);
		$identifier = md5($identifier);

		$SQL_PREPARE = $db->prepare('UPDATE com_shop_item SET identifier = :identifier WHERE id = :id');
		$SQL_PREPARE->execute(
			array(
				'id' => $id,
				'identifier' => $identifier
			)
		);
	}

	/**/

	$excel->createCell($arrayColumns['Идентификатор'],		$cellY, $identifier);
	$excel->createCell($arrayColumns['Наименование'],		$cellY,	$name);
	$excel->createCell($arrayColumns['Вводный текст'],		$cellY,	$intro_text);
	$excel->createCell($arrayColumns['Детальное описание'],	$cellY,	$full_text);
	$excel->createCell($arrayColumns['Цена'],				$cellY,	$price);
	$excel->createCell($arrayColumns['Старая цена'],	$cellY,	$price_old);
	$excel->createCell($arrayColumns['Распродажа'],			$cellY,	$discount);
	$excel->createCell($arrayColumns['Валюта'],				$cellY,	$currency);
	$excel->createCell($arrayColumns['Новинка'],			$cellY,	$new);
	$excel->createCell($arrayColumns['Хит'],				$cellY,	$hit);
	$excel->createCell($arrayColumns['Рейтинг'],			$cellY,	$rating);
	$excel->createCell($arrayColumns['Изображение'],		$cellY,	$photo);
	$excel->createCell($arrayColumns['Галерея'],			$cellY, $photo_more);
	$excel->createCell($arrayColumns['Действие'],			$cellY, $action);

	/* Характеристики */

	$chars = new Chars($item[0]['id']);
	$charsSource = $chars->getArray();
	$chars = Array();

	foreach($charsSource as $iter)
	{
		$charID = $iter['name_id'];
		$charValue = $iter['value'];

		if(array_key_exists($charID, $chars) && strlen($chars[$charID]) > 0)
			$chars[$charID] .= ';';

		@$chars[$charID] .= $charValue;
	}

	foreach($chars as $charID => $charValue)
	{
		if(key_exists($charID, $arrayColumnsChars))
			$excel->createCell($arrayColumnsChars[$charID], $cellY, $charValue);
	}

    /* Сопутствующие товары */

	$relatedItems = Shop\RelatedItems\getRelatedItemsById($id);
	$relatedItemsText = '';

	foreach($relatedItems as $iter)
		$relatedItemsText .= $iter['identifier'].';';

    $excel->createCell($arrayColumns['Сопутствующие товары'], $cellY, $relatedItemsText);

	/**/

	$INDEX++;

	if(time() - $TIME_START >= $TIME_MAX)
	{
		$excel->save($_SERVER['DOCUMENT_ROOT'].PATH_TO_PRICE);
		echo $INDEX;
		exit();
	}
}

exit();
