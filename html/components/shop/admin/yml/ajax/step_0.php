<?php
defined('AUTH') or die('Restricted access');

include_once $_SERVER['DOCUMENT_ROOT'].'/db.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/components/shop/classes/classShopSettings.php';

/**/

const XML_PATH = '/temp/yml.xml';

/* CREATE */

$xml = new DOMDocument('1.0', 'UTF-8');
//$xml->formatOutput = true;

/* ROOT */

$root = $xml->createElement('yml_catalog');
$root->setAttribute('date', (string)date("Y-m-d H:i"));
$xml->appendChild($root);

/* SHOP */

$shop = $xml->createElement('shop');
$root->appendChild($shop);

/* Name */

$name = $xml->createElement('name', ShopSettings::instance()->getValue('shop_name'));
$shop->appendChild($name);

/* Company */

$company = $xml->createElement('company', ShopSettings::instance()->getValue('company_name'));
$shop->appendChild($company);

/* Url */

$url = $xml->createElement('url', 'http://'.$site);
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
		$categoryParent->value = getSectionIdFromMenuId($iter['parent']);
		$category->appendChild($categoryParent);
	}
}

/* Offers */

$offers = $xml->createElement('offers');
$shop->appendChild($offers);

/* Save */

$xml->save($_SERVER['DOCUMENT_ROOT'].XML_PATH);

/* Return number of items */

$SQL_RESULT = $db->query('SELECT id FROM com_shop_item WHERE pub = "1"');
$numberOfItems = $SQL_RESULT->rowCount();
print($numberOfItems);
exit();

function getSectionIdFromMenuId($_menuId)
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
