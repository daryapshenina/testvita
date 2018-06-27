<?php
defined('AUTH') or die('Restricted access');

const PATH_TO_PRICE = '/temp/excel/price.xlsx';

include_once $_SERVER['DOCUMENT_ROOT']."/classes/Excel.php";
include_once $_SERVER['DOCUMENT_ROOT']."/components/shop/admin/classes/Items.php";
include_once $_SERVER['DOCUMENT_ROOT']."/components/shop/admin/classes/RelatedItems.php";

/**/

$excel = new Excel($_SERVER['DOCUMENT_ROOT'].PATH_TO_PRICE);

$TIME_MAX = ini_get('max_execution_time') - 5;
$TIME_START = time();
$INDEX = (int)$_POST['index'];
$MAX_INDEX = $excel->getSizeY();

$IDENTIFIER_COLUMN_NUMBER = -1;
$RELATED_ITEMS_COLUMN_NUMBER = -1;

/**/

for($x = 0;;$x++)
{
	$value = $excel->getCell($x, 0);

	if(strlen($value) === 0)
		break;

	if($value === 'Идентификатор')
		$IDENTIFIER_COLUMN_NUMBER = $x;

	if($value === 'Сопутствующие товары')
		$RELATED_ITEMS_COLUMN_NUMBER = $x;
}

if($IDENTIFIER_COLUMN_NUMBER === -1 || $RELATED_ITEMS_COLUMN_NUMBER === -1)
	exit("-1");

/**/

while(true)
{
	$identifier = $excel->getCell($IDENTIFIER_COLUMN_NUMBER, $INDEX);
	$relatedItemsRaw = $excel->getCell($RELATED_ITEMS_COLUMN_NUMBER, $INDEX);
	$relatedItems = explode(";", $relatedItemsRaw);

	/**/

	$item = Shop\Items\getItemByIdentifier($identifier);

	if(count($item) === 0)
		continue;

	Shop\RelatedItems\deleteAllRelatedItemsById($item[0]['id']);

	/**/

	foreach($relatedItems as $i => &$iter)
		Shop\RelatedItems\addRelatedItemByIdentifier($identifier, $iter, $i);

	/**/

	$INDEX++;

	// Если все товары загружены, сообщаем что загрузка закончена
	if($INDEX >= $MAX_INDEX)
		exit("-1");

	// Если время на выполнение скрипта вышло, возвращаем индекс
	if(time() - $TIME_START >= $TIME_MAX)
		exit($INDEX);

}
