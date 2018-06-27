<?php
defined('AUTH') or die('Restricted access');

include_once $_SERVER['DOCUMENT_ROOT'].'/db.php';
const XML_PATH = '/temp/yml.xml';

/* PARENT SECTION */

$ROOT_SECTION_ID = $_POST['root'];
$IDENTIFIER_PREFIX = 'yml_';

if(strlen($ROOT_SECTION_ID) > 0) $IDENTIFIER_PREFIX .= $ROOT_SECTION_ID.'_';

/* LOAD */

$xml = new DOMDocument('1.0', 'UTF-8');
$result = $xml->load($root . XML_PATH);

if(!$result) exit("error");


/* CATEGORIES */
$categories = $xml->getElementsByTagName('categories');
if($categories === null) exit("error");

$categories = $categories[0];


foreach($categories->childNodes as $childNode)
{
	if($childNode->nodeType === XML_TEXT_NODE) continue;

	$sectionID = -1;
	$title = $childNode->nodeValue;
	$identifier = $IDENTIFIER_PREFIX.$childNode->getAttribute('id');


	// section
	$stmt_section = $db->prepare('SELECT id FROM com_shop_section WHERE identifier = :identifier LIMIT 1');
	$stmt_section->execute(array('identifier' => $identifier));

	if($stmt_section->rowCount() > 0)
	{
		$section_id = $stmt_section->fetchColumn();

		// находим старые товары
		$stmt_item = $db->prepare("SELECT id, photo, photo_big, photo_more FROM com_shop_item WHERE section = :section AND cdate < DATE_SUB(NOW(), INTERVAL 1 DAY);");
		$stmt_item->execute(array('section' => $section_id));

		$photo_dir = $root.'/components/shop/photo/';

		foreach($stmt_item->fetchAll() as $item)
		{
			// если есть файл изображения и его имя не пустое - удяляем файлы изображения
			if (isset($item['photo']) && $item['photo'] != "")
			{
				if(is_file($photo_dir.$item['photo']))
				{
					unlink($photo_dir.$item['photo']);
				}

				if(is_file($photo_dir.$item['photo_big']))
				{
					unlink($photo_dir.$item['photo_big']);
				}
			}

			$photo_more_arr = explode(';', $item['photo_more']);

			for ($i = 0; $i < count($photo_more_arr); $i++)
			{
				if(is_file($photo_dir.$photo_more_arr[$i]))
				{
					unlink($photo_dir.$photo_more_arr[$i]);
					unlink(str_replace('.jpg', '_.jpg', $photo_dir.$photo_more_arr[$i]));
				}
			}

			$stmt_delete = $db->prepare('DELETE FROM com_shop_item WHERE id = :id');
			$stmt_delete->execute(array('id' => $item['id']));

			// Удаляем характеристики
			$stmt_char_delete = $db->prepare('DELETE FROM com_shop_char WHERE item_id = :item_id');
			$stmt_char_delete->execute(array('item_id' => $item['id']));

			// Удаляем сопутствующие товары
			$stmt_related_delete = $db->prepare('DELETE FROM com_shop_related_item WHERE item_id = :item_id OR related_id = :item_id');
			$stmt_related_delete->execute(array('item_id' => $item['id']));

			// Удаляем дополнительные цены
			$stmt_char_delete = $db->prepare('DELETE FROM com_shop_price_item WHERE item_id = :item_id');
			$stmt_char_delete->execute(array('item_id' => $item['id']));
		}
	}
}

echo 'success';
exit;

?>