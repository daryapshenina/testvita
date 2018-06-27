<?php
// Редактируем страницу
defined('AUTH') or die('Restricted access');

include_once($root.'/components/shop/classes/Chars.php');

$id = intval($d[5]);
$chars = new Chars($id);

$stmt_item = $db->prepare('SELECT * FROM com_shop_item WHERE id = :id LIMIT 1');
$stmt_item->execute(array('id' => $id));
$item = $stmt_item->fetch();

/* Копии фотографий */

$photoPath = $root.'/components/shop/photo/';
$photoCopyPrefix = substr(base64_encode(microtime()), 0, 9);
$photoCopyName = '';
$photoCopyNameBig = '';
$photoCopyMore = '';

if(is_file($photoPath.$item['photo']))
{
	$photoCopyName = $item['id'].$photoCopyPrefix.'.jpg';
	copy($photoPath.$item['photo'], $photoPath.$photoCopyName);
}

if(is_file($photoPath.$item['photo_big']))
{
	$photoCopyNameBig = $item['id'].$photoCopyPrefix.'_.jpg';
	copy($photoPath.$item['photo_big'], $photoPath.$photoCopyNameBig);
}

foreach(explode(';', $item['photo_more']) as $key => $value)
{
	if(is_file($photoPath.$value))
	{
		$oldName = ereg_replace(".jpg$", "", $value);
		$newName = $item['id'].$key.$photoCopyPrefix;

		copy($photoPath.$oldName.'.jpg', $photoPath.$newName.'.jpg');
		copy($photoPath.$oldName.'_.jpg', $photoPath.$newName.'_.jpg');

		$photoCopyMore .= $newName.".jpg;";
	}
}

/* Вставка в БД */

$stmt_item_insert = $db->prepare('
	INSERT INTO com_shop_item SET
		identifier = \'\',
		group_identifier = :group_identifier,
		section = :section,
		pub = :pub,
		parent = \'0\',
		ordering = :ordering,
		title = :title,
		intro_text = :intro_text,
		full_text = :full_text,
		etext_enabled = :etext_enabled,
		etext = :etext,
		price = :price,
		price_old = :price_old,
		currency = :currency,
		quantity = :quantity,
		photo = :photo,
		photo_big = :photo_big,
		photo_more = :photo_more,
		new = :new,
		discount = :discount,
		hit = :hit,
		rating = :rating,
		cdate = :cdate,
		tag_title = :tag_title,
		tag_description = :tag_description
');

$stmt_item_insert->execute(array(
	'section' => $item['section'],
	'group_identifier' => $item['group_identifier'],
	'pub' => $item['pub'],
	'ordering' => $item['ordering'],
	'title' => $item['title'].' - копия',
	'intro_text' => $item['intro_text'],
	'full_text' => $item['full_text'],
	'etext_enabled' => $item['etext_enabled'],
	'etext' => $item['etext'],
	'price' => $item['price'],
	'price_old' => $item['price_old'],
	'currency' => $item['currency'],
	'quantity' => $item['quantity'],
	'photo' => $photoCopyName,
	'photo_big' => $photoCopyNameBig,
	'photo_more' => $photoCopyMore,
	'new' => $item['new'],
	'discount' => $item['discount'],
	'hit' => $item['hit'],
	'rating' => $item['rating'],	
	'cdate' => date("Y-m-d H:i:s"),
	'tag_title' => $item['tag_title'],
	'tag_description' => $item['tag_description']
));

$last_item_id = $db->lastInsertId();

// Сопутствующие товары
$stmt_related_select = $db->prepare("SELECT * FROM com_shop_related_item WHERE item_id = :item_id");
$stmt_related_select->execute(array('item_id' => $id));

while($r = $stmt_related_select->fetch())
{
	$stmt_related_insert = $db->prepare('INSERT INTO com_shop_related_item SET item_id = :item_id, related_id = :related_id, ordering = :ordering');
	$stmt_related_insert->execute(array('item_id' => $last_item_id, 'related_id' => $r['related_id'], 'ordering' => $r['ordering']));	
}

/* Вставка характеристик */

$chars_arr = $chars->getArray($item['id']);

foreach($chars_arr as $row)
	Chars::addChar($last_item_id, $row['name_id'], $row['value'], $row['ordering'], $row['status']);

/**/

if(isset($d[6]) && $d[6] == 'frontend'){Header ("Location: /shop/section/".$item['section']);}
else{Header ("Location: /admin/com/shop/section/".$item['section']);}

exit;
