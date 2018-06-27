<?php
defined('AUTH') or die('Restricted access');

const PATH_TO_PRICE = '/temp/excel/price.xlsx';

const EXCEL_CURRENCY_RUB = 'rub';
const EXCEL_CURRENCY_USD = 'usd';
const EXCEL_CURRENCY_EUR = 'eur';

include_once $_SERVER['DOCUMENT_ROOT']."/db.php";
include_once $_SERVER['DOCUMENT_ROOT']."/classes/Excel.php";
include_once $_SERVER['DOCUMENT_ROOT']."/components/shop/admin/classes/Items.php";
include_once $_SERVER['DOCUMENT_ROOT']."/components/shop/admin/classes/Sections.php";
include_once $_SERVER['DOCUMENT_ROOT']."/components/shop/classes/Chars.php";

/**/

$excel = new Excel($_SERVER['DOCUMENT_ROOT'].PATH_TO_PRICE);

$TIME_MAX = ini_get('max_execution_time') - 5;
$TIME_START = time();
$INDEX = (int)$_POST['index'];
$MAX_INDEX = $excel->getSizeY();
$WITH_IMAGE = (bool)$_POST['with_image'];


/* СВОЙСТВА КОЛОНОК */

$maxDepthSections = 0;
$characteristics = array(); /* key = posX, value = charID */

$arrayColumns = array(
	'Идентификатор' => 999,
	'Наименование' => 999,
	'Вводный текст' => 999,
	'Детальное описание' => 999,
	'Цена' => 999,
	'Старая цена' => 999,
	'Распродажа' => 999,
	'Валюта' => 999,
	'Новинка' => 999,
	'Хит' => 999,
	'Рейтинг' => 999,
	'Изображение' => 999,
	'Галерея' => 999,
	'Действие' => 999
);


/* СЧИТАЕМ КОЛОНКИ */

for($x = 0;;$x++)
{
	$value = $excel->getCell($x, 0);

	if(strlen($value) == 0)
		break;

	if($value == 'Раздел')
	{
		$maxDepthSections++;
	}
	else
	{
		$arrayValue = explode(';', $value);

		if($arrayValue[0] == 'Характеристика')
		{
			$arrayChar = array();
			$name = '';
			$unit = '';
			$type = '';

			if(isset($arrayValue[1]))
				$name = (string)$arrayValue[1];

			if(isset($arrayValue[2]))
				$unit = (string)$arrayValue[2];

			if(isset($arrayValue[3]))
				$type = (string)$arrayValue[3];

			if(strlen($name) == 0)
				continue;

			if($type == 'число')
				$type = 'number';
			else
				$type = 'string';

			$charID = Chars::getNameID($name, $unit, $type);

			if($charID < 0)
				$charID = Chars::addName($name, $unit, $type, $x);
			else
				Chars::updateName($charID, null, null, null, $x);

			$characteristics[$x] = $charID;
		}
		else
		{
			foreach($arrayColumns as $key => &$iter)
			{
				if($key == $value)
					$iter = $x;
			}
		}
	}
}


/**/

while(true)
{
	/* ВСТАВЛЯЕМ КАТЕГОРИИ */

	$arraySections = array();
	for($i = 0;$i < $maxDepthSections;$i++)
	{
		$name = $excel->getCell($i, $INDEX);
		if(count($name) > 0)
			$arraySections[] = $name;
	}

	$MenuID	= Shop\Sections\createPath($arraySections);
	$section = Shop\Sections\getSectionByMenuID($MenuID);

	if(count($section) === 0)
	{
		$INDEX++;
		continue;
	}

	$sectionID = $section[0]['id_com'];


	/* ВСТАВЛЯЕМ ТОВАРЫ */

	$itemTitle		= $excel->getCell($arrayColumns['Наименование'], $INDEX);
	$itemIdentifier	= $excel->getCell($arrayColumns['Идентификатор'], $INDEX);
	$itemPub		= 1;
	$itemIntroText	= $excel->getCell($arrayColumns['Вводный текст'], $INDEX);
	$itemFullText	= $excel->getCell($arrayColumns['Детальное описание'], $INDEX);
	$itemPrice		= $excel->getCell($arrayColumns['Цена'], $INDEX);
	$itemPriceOld	= $excel->getCell($arrayColumns['Старая цена'], $INDEX);
	$itemCurrency	= $excel->getCell($arrayColumns['Валюта'], $INDEX);
	$itemPhoto		= $excel->getCell($arrayColumns['Изображение'], $INDEX);
	$itemGallery	= $excel->getCell($arrayColumns['Галерея'], $INDEX);
	$itemQuantity	= 1;
	$itemNew		= $excel->getCell($arrayColumns['Новинка'], $INDEX);
	$itemHit		= $excel->getCell($arrayColumns['Хит'], $INDEX);
	$itemRating		= $excel->getCell($arrayColumns['Рейтинг'], $INDEX);
	$itemDiscount	= $excel->getCell($arrayColumns['Распродажа'], $INDEX);

	$itemAction 	= $excel->getCell($arrayColumns['Действие'], $INDEX);

	// Действие

	if($itemAction == "удалить")
	{
		Shop\Items\deleteItemByIdentifier($itemIdentifier);
		$INDEX++;
		continue;
	}
	else if($itemAction == "скрыт")
	{
		$itemPub = 0;
	}

	// Валюта

	switch($itemCurrency)
	{
		case EXCEL_CURRENCY_USD:
			$itemCurrency = 1;
			break;

		case EXCEL_CURRENCY_EUR:
			$itemCurrency = 2;
			break;

		default:
			$itemCurrency = 0;
	}

	// Новый и распродажа

	if($itemNew == "Да")
		$itemNew = 1;
	else
		$itemNew = 0;

	if($itemHit == "Да")
		$itemHit = 1;
	else
		$itemHit = 0;

	if($itemDiscount == "Да")
		$itemDiscount = 1;
	else
		$itemDiscount = 0;

	// Если существует то обновить, иначе вставить
	$item = Shop\Items\getItemByIdentifier($itemIdentifier);
	$itemID = -1;

	if(count($item) > 0)
	{
		$itemID = $item[0]['id'];

		if($WITH_IMAGE)
		{
			Shop\Items\updateItem($itemID, $itemTitle, $sectionID, $itemIdentifier, $itemPub, $itemIntroText, $itemFullText, $itemPrice, $itemPriceOld, $itemCurrency, $itemPhoto, $itemGallery, $itemQuantity, $itemNew, $itemDiscount, $itemHit, $itemRating, $INDEX);
		}
		else
		{
			Shop\Items\updateItemWithoutImage($itemID, $itemTitle, $sectionID, $itemIdentifier, $itemPub, $itemIntroText, $itemFullText, $itemPrice, $itemPriceOld, $itemCurrency, $itemQuantity, $itemNew, $itemDiscount, $itemHit, $itemRating, $INDEX);
		}
	}
	else
	{
		$itemID = Shop\Items\addItem($itemTitle, $sectionID, $itemIdentifier, $itemPub, $itemIntroText, $itemFullText, $itemPrice, $itemPriceOld, $itemCurrency, $itemPhoto, $itemGallery, $itemQuantity, $itemNew, $itemDiscount, $itemHit, $itemRating, $INDEX);
	}

	// Вставляем характеристики
	if($itemID > 0)
	{
		$SQL_PREPARE = $db->prepare('DELETE FROM com_shop_char WHERE item_id = :item_id');
		$SQL_PREPARE->execute(
			array(
				'item_id' => $itemID
			)
		);

		foreach($characteristics as $numberColumn => $charID)
		{
			$charArrayValue = explode(';', $excel->getCell($numberColumn, $INDEX));

			foreach($charArrayValue as $charValue)
				Chars::addChar($itemID, $charID, $charValue, $numberColumn);
		}
	}

	/**/

	$INDEX++;

	// Если все товары загружены, сообщаем что загрузка закончена
	if($INDEX >= $MAX_INDEX)
	{
		echo '-1';
		exit();
	}

	// Если время на выполнение скрипта вышло, возвращаем индекс
	if(time() - $TIME_START >= $TIME_MAX)
	{
		echo $INDEX;
		exit();
	}
}

exit();
