<?php
defined('AUTH') or die('Restricted access');

include_once $_SERVER['DOCUMENT_ROOT'].'/db.php';

const XML_PATH = '/temp/yml.xml';

/* PARENT SECTION */

$ROOT_SECTION_ID = $_POST['root'];
$IDENTIFIER_PREFIX = 'yml_';

if(strlen($ROOT_SECTION_ID) > 0)
{
	$IDENTIFIER_PREFIX .= $ROOT_SECTION_ID . '_';

	$PREPARE = $db->prepare('UPDATE com_shop_section SET identifier = :identifier WHERE id = :id LIMIT 1');

	$PREPARE->execute(
		array(
			'id' => $ROOT_SECTION_ID,
			'identifier' => $IDENTIFIER_PREFIX
		)
	);
}

/* LOAD FILE */

if($_FILES['file'] === null)
	exit('error');

move_uploaded_file($_FILES['file']['tmp_name'], $root . XML_PATH);

/* LOAD */

$xml = new DOMDocument('1.0', 'UTF-8');
$result = $xml->load($root . XML_PATH);

if(!$result)
	exit("error");

/* CATEGORIES */

$categories = $xml->getElementsByTagName('categories');

if($categories === null)
	exit("error");

$categories = $categories[0];

foreach($categories->childNodes as $childNode)
{
	if($childNode->nodeType === XML_TEXT_NODE)
		continue;

	$sectionID = -1;
	$title = $childNode->nodeValue;
	$identifier = $IDENTIFIER_PREFIX . $childNode->getAttribute('id');

	// section

	$PREPARE = $db->prepare('SELECT * FROM com_shop_section WHERE identifier = :identifier LIMIT 1');

	$PREPARE->execute(
		array(
			'identifier' => $identifier
		)
	);

	if($PREPARE->rowCount() === 0)
	{
		$PREPARE = $db->prepare('INSERT INTO com_shop_section SET identifier = :identifier, pub = 1, parent = 0, title = :title, date = NOW()');

		$PREPARE->execute(
			array(
				'identifier' => $identifier,
				'title' => $title
			)
		);

		$sectionID = $db->lastInsertId();
	}
	else
	{
		$sectionID = $PREPARE->fetch()['id'];

		$PREPARE = $db->prepare('UPDATE com_shop_section SET title = :title WHERE identifier = :identifier LIMIT 1');

		$PREPARE->execute(
			array(
				'identifier' => $identifier,
				'title' => $title
			)
		);
	}

	// menu

	$PREPARE = $db->prepare('SELECT * FROM menu WHERE component = "shop" AND p1 = "section" AND id_com = :id');

	$PREPARE->execute(
		array(
			'id' => $sectionID
		)
	);

	if($PREPARE->rowCount() === 0)
	{
		$PREPARE = $db->prepare('INSERT INTO menu SET menu_type = "left", name = :name, pub = 1, parent = 0, ordering = :id_com, component = "shop", main = 0, p1 = "section", id_com = :id_com');

		$PREPARE->execute(
			array(
				'id_com' => $sectionID,
				'name' => $title
			)
		);
	}
	else
	{
		$PREPARE = $db->prepare('UPDATE menu SET name = :name, parent = 0 WHERE component = "shop" AND p1 = "section" AND id_com = :id_com LIMIT 1');

		$PREPARE->execute(
			array(
				'id_com' => $sectionID,
				'name' => $title
			)
		);
	}
}

/* SET PARENTS */

foreach($categories->childNodes as $childNode)
{
	if($childNode->nodeType === XML_TEXT_NODE)
		continue;

	$parentID = $childNode->hasAttribute('parentId') ? $childNode->getAttribute('parentId') : '';
	$childID = $childNode->getAttribute('id');

	$parentIdentifier = $IDENTIFIER_PREFIX . $parentID;
	$childIdentifier = $IDENTIFIER_PREFIX . $childID;

	if(strlen($parentID) === 0)
	{
		if(strlen($ROOT_SECTION_ID) === 0)
			continue;

		$parentIdentifier = $IDENTIFIER_PREFIX;
	}

	$parentMenu = getMenuBySectionIdentifier($parentIdentifier);
	$childMenu = getMenuBySectionIdentifier($childIdentifier);

	if($parentMenu === null || $childMenu === null)
		continue;

	$PREPARE = $db->prepare('UPDATE menu SET menu_type = :menu_type, parent = :parent WHERE component = "shop" AND p1 = "section" AND id = :id LIMIT 1');

	$PREPARE->execute(
		array(
			'id' => $childMenu['id'],
			'menu_type' => $parentMenu["menu_type"],
			'parent' => $parentMenu["id"]
		)
	);
}

/* RETURN NUMBER OF ITEMS */

$offers = $xml->getElementsByTagName('offers');
$offersCount = 0;

if($offers->length === 0)
{
	print(0);
	exit();
}

foreach($offers[0]->childNodes as $iter)
	if($iter->nodeType !== XML_TEXT_NODE)
		$offersCount++;

print($offersCount);
exit();

function getMenuBySectionIdentifier($_identifier)
{
	global $db;

	$SQL_RESULT = $db->prepare('SELECT * FROM com_shop_section WHERE identifier = :identifier LIMIT 1');
	$SQL_RESULT->execute(
		array(
			'identifier' => $_identifier,
		)
	);

	$section = $SQL_RESULT->fetchAll();

	if(count($section) === 0)
		return null;

	$section = $section[0];

	$SQL_RESULT = $db->prepare('SELECT * FROM menu WHERE component = "shop" AND p1 = "section" AND id_com = :id_com LIMIT 1');
	$SQL_RESULT->execute(
		array(
			'id_com' => $section['id']
		)
	);

	$menu = $SQL_RESULT->fetchAll();

	if(count($menu) === 0)
		return null;

	return $menu[0];
}
