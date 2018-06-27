<?php
defined('AUTH') or die('Restricted access');

header("Content-type: text/xml");

include_once $root.'/db.php';
include_once $root.'/components/shop/classes/classShopSettings.php';
include_once $root.'/components/shop/classes/Chars.php';

if($d[2] !== $shopSettings->yml_key) exit();

$protocol = "http://";

if(isset($_SERVER['HTTPS']))
	$protocol = "https://";

/* CREATE */

$xml = new DOMDocument('1.0', 'UTF-8');
$xml->formatOutput = true;

/* ROOT */

$root = $xml->createElement('yml_catalog');
$root->setAttribute('date', (string)date("Y-m-d H:i"));
$xml->appendChild($root);

/* SHOP */

$shop = $xml->createElement('shop');
$root->appendChild($shop);

/* Name */

$name = $xml->createElement('name', $shopSettings->shop_name);
$shop->appendChild($name);

/* Company */

$company = $xml->createElement('company', $shopSettings->company_name);
$shop->appendChild($company);

/* Url */

$url = $xml->createElement('url', $protocol.$site);
$shop->appendChild($url);

/* Currencies */

$currencies = $xml->createElement('currencies');
$shop->appendChild($currencies);

$currencyRub = $xml->createElement('currency');
$currencies->appendChild($currencyRub);

$currencyRubID = $xml->createAttribute('id');
$currencyRubID->value = 'RUR';
$currencyRub->appendChild($currencyRubID);

$currencyRubRate = $xml->createAttribute('rate');
$currencyRubRate->value = '1';
$currencyRub->appendChild($currencyRubRate);

/* Categories */

$categories = $xml->createElement('categories');
$shop->appendChild($categories);

$SQL_RESULT = $db->query('SELECT id, name, parent, id_com FROM menu WHERE component = "shop" AND p1 = "section"');
$arrayCategories = $SQL_RESULT->fetchAll();

foreach($arrayCategories as &$iter)
{
	$category = $xml->createElement('category', $iter['name']);
	$categories->appendChild($category);

	$categoryID = $xml->createAttribute('id');
	$categoryID->value = $iter['id_com'];
	$category->appendChild($categoryID);

	if($iter['parent'] > 0)
	{
		$categoryParent = $xml->createAttribute('parentId');
		$categoryParent->value = getSectionIDFromMenuID($iter['parent']);
		$category->appendChild($categoryParent);
	}
}

/* Offers */

$offers = $xml->createElement('offers');
$shop->appendChild($offers);

/* Currency */

CCurrency::update();

/* Items */

$SQL_RESULT = $db->query('SELECT * FROM com_shop_item WHERE pub = "1"');
$items = $SQL_RESULT->fetchAll();

foreach($items as &$iter)
{
	$itemNode = $xml->createElement('offer');
	$offers->appendChild($itemNode);

	// id
	$itemID = $xml->createAttribute('id');
	$itemID->value = $iter['id'];
	$itemNode->appendChild($itemID);

	// url
	$itemURL = $xml->createElement('url', $protocol.$site.'/shop/item/'.$iter['id']);
	$itemNode->appendChild($itemURL);

	// currency conversion
	switch($iter['currency'])
	{
		case 1:
		{
			$iter['price'] *= CCurrency::getUSD();
			$iter['price_old'] *= CCurrency::getUSD();
		} break;

		case 2:
		{
			$iter['price'] *= CCurrency::getEUR();
			$iter['price_old'] *= CCurrency::getEUR();
		} break;
	}

	$iter['price'] = floor($iter['price']);
	$iter['price_old'] = floor($iter['price_old']);

	// price
	$itemPrice = $xml->createElement('price', $iter['price']);
	$itemNode->appendChild($itemPrice);

	// old price
	if($iter['discount'] && $iter['price'] < $iter['price_old'])
	{
		$itemOldPrice = $xml->createElement('oldprice', $iter['price_old']);
		$itemNode->appendChild($itemOldPrice);
	}

	// currency
	$itemCurrency = $xml->createElement('currencyId', 'RUR');
	$itemNode->appendChild($itemCurrency);

	// category id
	$itemCategoryID = $xml->createElement('categoryId', $iter['section']);
	$itemNode->appendChild($itemCategoryID);

	// picture
	$photos = Array();
	array_push($photos, $iter['photo_big']);

	$gallerySrc = $iter['photo_more'];
	$gallerySrc = explode(";", $gallerySrc);

	foreach($gallerySrc as &$i)
	{
		$i = str_replace(".jpg", "_.jpg", $i);
		array_push($photos, $i);
	}

	foreach($photos as $i)
	{
		if(!is_file($_SERVER['DOCUMENT_ROOT'].'/components/shop/photo/'.$i))
			continue;

		$itemPhoto = $xml->createElement('picture', $protocol.$site.'/components/shop/photo/'.$i);
		$itemNode->appendChild($itemPhoto);
	}

	// name
	$itemName = $xml->createElement('name', htmlspecialchars(pregtrim($iter['title'])));
	$itemNode->appendChild($itemName);

	// description
	$itemDescription = $xml->createElement('description');
	$itemNode->appendChild($itemDescription);

	$iter['full_text'] = strip_tags($iter['full_text'], "<h3><ul><li><p><br>");
	$iter['full_text'] = htmlspecialchars($iter['full_text']);
	$itemDescriptionCDate = $xml->createCDATASection($iter['full_text']);
	$itemDescription->appendChild($itemDescriptionCDate);

	// sales notes
	$itemSalesNotes = $xml->createElement('sales_notes', $shopSettings->delivery);
	$itemNode->appendChild($itemSalesNotes);

	// stock
	$nodeStock = $xml->createElement('stock', $iter['quantity']);
	$itemNode->appendChild($nodeStock);

	// characteristics
	$characteristics = new Chars($iter['id']);
	$characteristics = $characteristics->getArray();

	foreach($characteristics as &$iter)
	{
		if($iter['name'] === 'Производитель' && $iter['unit'] === '')
		{
			$nodeVendor = $xml->createElement('vendor', $iter['value']);
			$itemNode->appendChild($nodeVendor);
			continue;
		}

		if($iter['name'] === 'Код производителя' && $iter['unit'] === '')
		{
			$nodeVendorCode = $xml->createElement('vendorCode', $iter['value']);
			$itemNode->appendChild($nodeVendorCode);
			continue;
		}

		if($iter['name'] === 'Вес' && $iter['unit'] === 'кг.')
		{
			$nodeWeight = $xml->createElement('weight', $iter['value']);
			$itemNode->appendChild($nodeWeight);
			continue;
		}

		$paramNode = $xml->createElement('param', $iter['value']);
		$itemNode->appendChild($paramNode);

		$attrName = $xml->createAttribute('name');
		$attrName->value = $iter['name'];
		$paramNode->appendChild($attrName);

		$attrUnit = $xml->createAttribute('unit');
		$attrUnit->value = $iter['unit'];
		$paramNode->appendChild($attrUnit);
	}

}

/* Print */

print_r($xml->saveXML());
exit();

function getSectionIDFromMenuID($_menuId)
{
	global $db;

	$SQL_RESULT = $db->prepare('SELECT id_com FROM menu WHERE id = :id LIMIT 1');
	$SQL_RESULT->execute(
		array(
			'id' => $_menuId,
		)
	);

	$category = $SQL_RESULT->fetchAll();

	if(count($category) > 0)
		return $category[0]['id_com'];

	return 0;
}
