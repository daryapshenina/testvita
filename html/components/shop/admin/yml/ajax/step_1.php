<?php
defined('AUTH') or die('Restricted access');

include_once $_SERVER['DOCUMENT_ROOT'].'/db.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/lib/lib.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/lib/currency.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/components/shop/classes/classShopSettings.php';

/**/

const XML_PATH = '/temp/yml.xml';

$TIME_MAX = ini_get('max_execution_time') - 5;
$TIME_START = time();
$INDEX = (int)$_POST['index'];

/**/

$xml = new DOMDocument('1.0', 'UTF-8');
//$xml->formatOutput = true;
$xml->load($_SERVER['DOCUMENT_ROOT'].XML_PATH);

$offers = $xml->getElementsByTagName('offers');
$offers = $offers[0];

CCurrency::update();

/**/

while(true)
{
	$SQL_RESULT = $db->prepare('SELECT * FROM com_shop_item WHERE pub = "1" LIMIT 1 OFFSET :offset');
	$SQL_RESULT->bindValue(':offset', (int)$INDEX, PDO::PARAM_INT);
	$SQL_RESULT->execute();

	$item = $SQL_RESULT->fetchAll();

	/* IS END? */

	if(count($item) === 0)
	{
		$xml->save($_SERVER['DOCUMENT_ROOT'].XML_PATH);
		exit('-1');
	}

	$item = $item[0];

	/* ADD ITEM */

	$itemNode = $xml->createElement('offer');
	$offers->appendChild($itemNode);

	// id
	$itemID = $xml->createAttribute('id');
	$itemID->value = $item['id'];
	$itemNode->appendChild($itemID);

	// url
	$itemURL = $xml->createElement('url', 'http://'.$site.'/shop/item/'.$item['id']);
	$itemNode->appendChild($itemURL);

	// currency conversion
	switch($item['currency'])
	{
		case 1:
		{
			$item['price'] *= CCurrency::getUSD();
			$item['price_old'] *= CCurrency::getUSD();
		} break;

		case 2:
		{
			$item['price'] *= CCurrency::getEUR();
			$item['price_old'] *= CCurrency::getEUR();
		} break;
	}

	$item['price'] = floor($item['price']);
	$item['price_old'] = floor($item['price_old']);

	// price
	$itemPrice = $xml->createElement('price', $item['price']);
	$itemNode->appendChild($itemPrice);

	// old price
	if($item['discount'] && $item['price'] < $item['price_old'])
	{
		$itemOldPrice = $xml->createElement('oldprice', $item['price_old']);
		$itemNode->appendChild($itemOldPrice);
	}

	// currency
	$itemCurrency = $xml->createElement('currencyId', 'RUR');
	$itemNode->appendChild($itemCurrency);

	// category id
	$itemCategoryID = $xml->createElement('categoryId', $item['section']);
	$itemNode->appendChild($itemCategoryID);

	// category name
	$SQL_RESULT = $db->prepare('SELECT title FROM com_shop_section WHERE id = :id LIMIT 1');
	$SQL_RESULT->execute(
		array(
			'id' => $item['section'],
		)
	);

	$category = $SQL_RESULT->fetchAll();

	if(count($category) > 0)
	{
		$itemCategoryName = $xml->createElement('categoryId', $category[0]['title']);
		$itemNode->appendChild($itemCategoryName);
	}

	// picture
	$photos = Array();
	array_push($photos, $item['photo_big']);

	$gallerySrc = $item['photo_more'];
	$gallerySrc = explode(";", $gallerySrc);

	foreach($gallerySrc as $iter)
		array_push($photos, $iter);

	foreach($photos as $iter)
	{
		if(is_file($_SERVER['DOCUMENT_ROOT'].'/components/shop/photo/'.$iter))
		{
			$itemPhoto = $xml->createElement('picture', 'http://'.$site.'/components/shop/photo/'.$iter);
			$itemNode->appendChild($itemPhoto);
		}
	}

	// name
	$itemName = $xml->createElement('name', htmlspecialchars(pregtrim($item['title'])));
	$itemNode->appendChild($itemName);

	// description
	$itemDescription = $xml->createElement('description');
	$itemNode->appendChild($itemDescription);

	$item['intro_text'] = strip_tags($item['intro_text'], "<h3><ul><li><p><br>");
	$itemDescriptionCDate = $xml->createCDATASection($item['intro_text']);
	$itemDescription->appendChild($itemDescriptionCDate);

	// sales notes
	$itemSalesNotes = $xml->createElement('sales_notes', ShopSettings::instance()->getValue('delivery'));
	$itemNode->appendChild($itemSalesNotes);

	/* TIME IS OVER? */

	$INDEX++;

	if(time() - $TIME_START >= $TIME_MAX)
	{
		$xml->save($_SERVER['DOCUMENT_ROOT'].XML_PATH);
		print($INDEX);
		exit();
	}
}

exit();
