<?php
defined('AUTH') or die('Restricted access');

ini_set('error_reporting', E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

include_once $_SERVER['DOCUMENT_ROOT'].'/db.php';
include_once $_SERVER['DOCUMENT_ROOT']."/components/shop/classes/Chars.php";
include_once $_SERVER['DOCUMENT_ROOT']."/components/shop/admin/classes/Photo.php";

const XML_PATH = '/temp/yml.xml';

@mkdir($root . '/temp/yml/');

/* TIME */

$TIME_MAX = ini_get('max_execution_time') - 10;
$TIME_START = time();
$INDEX = (int)$_POST['index'];

/* PARENT SECTION */

$ROOT_SECTION_ID = $_POST['root'];
$IDENTIFIER_PREFIX = 'yml_';

if(strlen($ROOT_SECTION_ID) > 0)
	$IDENTIFIER_PREFIX .= $ROOT_SECTION_ID . '_';

/* LOAD */

$xml = new XMLReader();
$xml->open($root . XML_PATH);

for($i = 0;$xml->read();)
{
	if($xml->nodeType !== XMLReader::ELEMENT || $xml->name !== 'offer')
		continue;

	if($i++ < $INDEX)
		continue;

	$INDEX = $i;
	parseItem($xml->expand());

	if(time() - $TIME_START >= $TIME_MAX)
		break;
}

$xml->close();
print($INDEX);
exit();

function parseItem($_item)
{
	global $db, $root, $IDENTIFIER_PREFIX;

	$NEW = $_POST['new'];
	$DISCOUNT = $_POST['sale'];	


	$item_id = -1;
	$identifier = '';
	$price = 0;
	$oldprice = 0;
	$discount = 0;
	$categoryID = -1;
	$categoryYML = '';
	$name = '';
	$description = '';
	$stock = 1.0;
	$characteristics = array();

	// price

	$nodePrice = $_item->getElementsByTagName("price");

	if($nodePrice->length > 0)
		$price = $nodePrice[0]->nodeValue;

	// old price

	$nodePriceOld = $_item->getElementsByTagName("oldprice");

	if($nodePriceOld->length > 0)
		$oldprice = $nodePriceOld[0]->nodeValue;

	if($oldprice > 0 || $DISCOUNT == 1) $discount = 1;

	// category

	$nodeCategory = $_item->getElementsByTagName("categoryId");

	if($nodeCategory->length > 0)
	{
		$categoryYML = $nodeCategory[0]->nodeValue;
		$category = getSectionByIdentifier($IDENTIFIER_PREFIX . $categoryYML);

		if($category === null)
			return;

		$categoryID = $category['id'];
	}

	if($categoryID === -1)
		return;

	// identifier

	if($_item->hasAttribute("id"))
		$identifier = $IDENTIFIER_PREFIX . $categoryYML . '_' . $_item->getAttribute("id");

	if(strlen($identifier) === 0)
		return;

	// picture

	$nodePictures = $_item->getElementsByTagName("picture");

	$picturesFiles = [
		'tmp_name' => []
	];

	foreach($nodePictures as $i => $childNode)
	{
		if($childNode->nodeType === XML_TEXT_NODE)
			continue;

		$url = $childNode->nodeValue;
		$headers = get_headers($url);

		if($headers[0] !== 'HTTP/1.1 200 OK')
			continue;

		$image = file_get_contents($url);

		if(!$image)
			continue;

		$tempImageName = $root . "/temp/yml/yml_image_temp_" . $i;		$tempImageName = $root . "/temp/yml/yml_image_temp_" . $i . ".jpg";

		if(file_put_contents($tempImageName, $image) !== false)
			array_push($picturesFiles['tmp_name'], $tempImageName);
	}

	// name

	$nodeName = $_item->getElementsByTagName("name");

	if($nodeName->length > 0)
	{
		$title_1 = $nodeName[0]->nodeValue.'. ';
	}
	else
	{
		$title_1 = '';
	}

	$nodeModel = $_item->getElementsByTagName("model");
	if($nodeModel->length > 0)
	{
		$title_2 = $nodeModel[0]->nodeValue.'. ';
		if($title_2 == $title_1) $title_2 = '';
	}
	else
	{
		$title_2 = '';
	}

	$nodeVendorCode = $_item->getElementsByTagName("vendorCode");
	if($nodeVendorCode->length > 0)
	{
		$title_3 = $nodeVendorCode[0]->nodeValue;
		if($title_3.'. ' == $title_1 || $title_3.'. ' == $title_2) $title_3 = '';
	}
	else
	{
		$title_3 = '';
	}


	$title = $title_1.$title_2.$title_3;


	// description

	$nodeDescription = $_item->getElementsByTagName("description");

	if($nodeDescription->length > 0)
		$description = htmlspecialchars_decode($nodeDescription[0]->nodeValue);

	// stock

	$nodeStock = $_item->getElementsByTagName("stock");

	if($nodeStock->length > 0)
		$stock = $nodeStock[0]->nodeValue;

	// Insert or Update
	$stmt_section = $db->prepare('SELECT id FROM com_shop_item WHERE identifier = :identifier LIMIT 1');

	$stmt_section->execute(
		array(
			'identifier' => $identifier
		)
	);

	$item_id = $stmt_section->fetchColumn();

	if($stmt_section->rowCount() === 0) // Вставляем товар
	{

		// Находим ordering
		$stmt_ordering = $db->prepare('SELECT MAX(ordering) FROM com_shop_item WHERE section =:section_id');
		$stmt_ordering->execute(array('section_id' => $categoryID));
		$ordering = $stmt_ordering->fetchColumn() + 1;

		if(intval($_POST['price_main']) > 0) $price = $price * (intval($_POST['price_main']) + 100)/100;

		$PREPARE = $db->prepare('
			INSERT INTO com_shop_item SET
				identifier = :identifier,
				section = :section,
				pub = 1,
				parent = 0,
				ordering = :ordering,
				title = :title,
				full_text = :full_text,
				etext_enabled = 0,
				price = :price,
				price_old = :price_old,
				currency = 0,
				quantity = :quantity,
				new = :new,
				discount = :discount,
				hit = 0,
				rating = 0.0,
				cdate = NOW()
		');

		$PREPARE->execute(
			array(
				"identifier" => $identifier,
				"section" => $categoryID,
				"ordering" => $ordering,
				"title" => $title,
				"full_text" => $description,
				"price" => $price,
				"price_old" => $oldprice,
				"quantity" => $stock,
				"new" => $NEW,
				"discount" => $discount
			)
		);

		$item_id = $db->lastInsertId();

		// --- ТИПЫ ЦЕН ---
		if(!empty($_POST['price_user']))
		{
			foreach($_POST['price_user'] as $price_type_id => $price_item)
			{
				if(intval($price_item) != 0)
				{
					$price = $price * (intval($price_item) + 100)/100;

					$stmt_pi_insert = $db->prepare("INSERT INTO com_shop_price_item SET item_id = :item_id, price_type_id = :price_type_id, price = :price");
					$stmt_pi_insert->execute(array('item_id' => $item_id, 'price_type_id' => $price_type_id, 'price' => $price));
				}
			}
		}

	}
	else // Обновляем товар
	{
		if(intval($_POST['price_main']) > 0){$price_main = $price * (intval($_POST['price_main']) + 100)/100;} else{$price_main = $price;}

		$PREPARE = $db->prepare('
			UPDATE com_shop_item SET
				section = :section,
				title = :title,
				full_text = :full_text,
				price = :price,
				price_old = :price_old,
				quantity = :quantity,
				new = :new,
				discount = :discount,
				cdate = NOW()
			WHERE identifier = :identifier LIMIT 1');

		$PREPARE->execute(
			array(
				"identifier" => $identifier,
				"section" => $categoryID,
				"title" => $title,
				"full_text" => $description,
				"price" => $price_main,
				"price_old" => $oldprice,
				"quantity" => $stock,
				"new" => $NEW,
				"discount" => $discount
			));



		// --- ТИПЫ ЦЕН ---
		if(!empty($_POST['price_user']))
		{
			foreach($_POST['price_user'] as $price_type_id => $price_item)
			{
				$stmt_pi = $db->prepare("SELECT id FROM com_shop_price_item WHERE item_id = :item_id AND price_type_id = :price_type_id LIMIT 1");
				$stmt_pi->execute(array('item_id' => $item_id, 'price_type_id' => $price_type_id));
				$pi_id = $stmt_pi->fetchColumn();

				if(intval($price_item) != 0)
				{
					if($stmt_pi->rowCount() > 0) // Уже есть эта цена - обновляем
					{
						$price_i = $price * ($price_item + 100) / 100;
						$stmt_pi_update = $db->prepare("UPDATE com_shop_price_item SET price = :price WHERE id = :id");
						$stmt_pi_update->execute(array('id' => $pi_id, 'price' => $price_i));
					}
					else // Нет цены - добавляем
					{
						$price_i = $price * ($price_item + 100) / 100;
						$stmt_pi_update = $db->prepare("INSERT INTO com_shop_price_item SET item_id = :item_id, price_type_id = :price_type_id, price = :price");
						$stmt_pi_update->execute(array('item_id' => $item_id, 'price_type_id' => $price_type_id, 'price' => $price_i));
					}
				}
				else // Удаляем пустые значения, незачем хранить мусор
				{
					if($stmt_pi->rowCount() > 0) // Уже есть эта цена - удаляем
					{
						$stmt_pi_delete = $db->prepare("DELETE FROM com_shop_price_item WHERE id = :id");
						$stmt_pi_delete->execute(array('id' => $pi_id));
					}
				}
			}
		}
	}

	// Photo insert

	Shop\Photo\deletePhoto($item_id);

	if(count($picturesFiles['tmp_name']) > 0)
		Shop\Photo\updatePhoto($item_id, $picturesFiles);

	// Delete Characteristics

	$SQL_PREPARE = $db->prepare('DELETE FROM com_shop_char WHERE item_id = :item_id');
	$SQL_PREPARE->execute(
		array(
			'item_id' => $item_id
		)
	);

	// Params

	$nodeParams = $_item->getElementsByTagName("param");

	foreach($nodeParams as $i => $childNode)
	{
		if($childNode->nodeType === XML_TEXT_NODE)
			continue;

		if(!$childNode->hasAttribute("name"))
			continue;

		$name = $childNode->getAttribute("name");
		$unit = $childNode->hasAttribute("unit") ? $childNode->getAttribute("unit") : "";
		$value = $childNode->nodeValue;

		array_push($characteristics, array(
			"name" => $name,
			"unit" => $unit,
			"value" => $value
		));
	}

	// Weight

	$nodeWeight = $_item->getElementsByTagName("weight");

	if($nodeWeight->length > 0)
	{
		array_unshift($characteristics, array(
			"name" => "Вес",
			"unit" => "кг.",
			"value" => $nodeWeight[0]->nodeValue
		));
	}

	// Vendor code

	$nodeVendorCode = $_item->getElementsByTagName("vendorCode");

	if($nodeVendorCode->length > 0)
	{
		array_unshift($characteristics, array(
			"name" => "Код производителя",
			"unit" => "",
			"value" => $nodeVendorCode[0]->nodeValue
		));
	}

	// Vendor

	$nodeVendor = $_item->getElementsByTagName("vendor");

	if($nodeVendor->length > 0)
	{
		array_unshift($characteristics, array(
			"name" => "Производитель",
			"unit" => "",
			"value" => $nodeVendor[0]->nodeValue
		));
	}

	// Add characteristic

	foreach($characteristics as $i => $iter)
	{
		$charID = Chars::getNameID($iter['name'], $iter['unit'], "string");

		if($charID < 0)
			$charID = Chars::addName($iter['name'], $iter['unit'], "string");

		Chars::addChar($item_id, $charID, $iter['value'], $i);
	}

}

exit();

function getSectionByIdentifier($_identifier)
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

	return $section[0];
}


function log_file ($_text)
{
	global $root;

	$dir = '/components/shop/admin/yml/';

	$file = $root.$dir.'step_1.log';
	$f = fopen($file,"a+");
	fwrite($f,$_text);
	fclose($f);
}
