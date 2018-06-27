<?php
namespace Shop\Items;
defined('AUTH') or die('Restricted access');

include_once $_SERVER['DOCUMENT_ROOT']."/db.php";
include_once __DIR__.'/Sections.php';

/*
	Добавить товар.
	Возвращает ID вставленного товара или значение ниже нуля в случае ошибки.
	Время создание добавить.
*/

function addItem($_title, $_sectionID, $_identifier = '',  $_pub = 1,
					$_introText = '', $_fullText = '',
					$_price = 0, $_priceOld = 0, $_currency = 0,
					$_photo = '', $_gallery = '',
					$_quantity = 1, $_new = 0, $_discount = 0, $_hit = 0, $_rating = 0,
					$_ordering = 0)
{
	global $db;

	$_title = (string)$_title;
	$_sectionID = (int)$_sectionID;
	$_identifier = (string)$_identifier;
	$_pub = (int)$_pub;
	$_introText = (string)$_introText;
	$_fullText = (string)$_fullText;
	$_price = (string)$_price;
	$_priceOld = (string)$_priceOld;
	$_currency = (int)$_currency;
	$_photo = (string)$_photo;
	$_gallery = (string)$_gallery;
	$_quantity = (int)$_quantity;
	$_new = (int)$_new;
	$_hit = (int)$_hit;
	$_rating = (int)$_rating;
	$_discount = (int)$_discount;
	$_ordering = (int)$_ordering;

	$arraySection = \Shop\Sections\getSectionByID($_sectionID);

	if(count($arraySection) == 0 || strlen($_title) == 0)
		return -1;

	$SQL_PREPARE = $db->prepare('INSERT INTO com_shop_item
									(identifier, section, pub, parent, title, intro_text, full_text, price, price_old, currency, quantity, photo, photo_big, photo_more, new, discount, hit, rating, ordering, cdate) VALUES
									(:identifier, :section, :pub, :parent, :title, :intro_text, :full_text, :price, :price_old, :currency, :quantity, :photo, :photo_big, :photo_more, :new, :discount, :hit, :rating, :ordering, NOW())'
								);

	$SQL_PREPARE->execute(
		array(
			'identifier' => $_identifier,
			'section' => $_sectionID,
			'pub' => $_pub,
			'parent' => 1,
			'title' => $_title,
			'intro_text' => $_introText,
			'full_text' => $_fullText,
			'price' => $_price,
			'price_old' => $_priceOld,
			'currency' => $_currency,
			'photo' => $_photo,
			'photo_big' => $_photo,
			'photo_more' => $_gallery,
			'quantity' => $_quantity,
			'new' =>  $_new,
			'discount' => $_discount,
			'hit' => $_hit,
			'rating' => $_rating,
			'ordering' => $_ordering
		)
	);

	return $db->lastInsertId();
}


/*
	Обновить товар.
*/

function updateItem($_ID, $_title, $_sectionID, $_identifier, $_pub = 1,
						$_introText = '', $_fullText = '',
						$_price = 0, $_priceOld = 0, $_currency = 0,
						$_photo = '', $_gallery = '',
						$_quantity = 1, $_new = 0, $_discount = 0, $_hit = 0, $_rating = 0,
						$_ordering = 0)
{
	global $db;

	$_ID = (int)$_ID;
	$_title = (string)$_title;
	$_sectionID = (int)$_sectionID;
	$_identifier = (string)$_identifier;
	$_pub = (int)$_pub;
	$_introText = (string)$_introText;
	$_fullText = (string)$_fullText;
	$_price = (string)$_price;
	$_priceOld = (string)$_priceOld;
	$_currency = (int)$_currency;
	$_photo = (string)$_photo;
	$_gallery = (string)$_gallery;
	$_quantity = (int)$_quantity;
	$_new = (int)$_new;
	$_hit = (int)$_hit;
	$_rating = (int)$_rating;
	$_discount = (int)$_discount;
	$_ordering = (int)$_ordering;

	$arraySection = \Shop\Sections\getSectionByID($_sectionID);

	if(count($arraySection) == 0 || strlen($_title) == 0)
		return false;

	$item = getItemByID($_ID);

	if(count($item) == 0)
		return false;

	$arrayPhoto = array();
	$arrayPhoto[] = $item[0]['photo'];
	$arrayPhoto[] = $item[0]['photo_big'];

	$gallery = explode(';', $item[0]['photo_more']);

	foreach($gallery as &$iter)
	{
		if(strlen($iter) > 0)
		{
			$arrayPhoto[] = $iter;
			$arrayPhoto[] = str_replace('.jpg', '_.jpg', $iter);;
		}
	}

	foreach($arrayPhoto as $iter)
	{
		if(is_file($_SERVER['DOCUMENT_ROOT'].'/components/shop/photo/'.$iter))
			@unlink($_SERVER['DOCUMENT_ROOT'].'/components/shop/photo/'.$iter);
	}

	$SQL_PREPARE = $db->prepare('UPDATE com_shop_item SET identifier = :identifier, section = :section, pub = :pub, parent = 1, title = :title,
									intro_text = :intro_text, full_text = :full_text, price = :price, price_old = :price_old, currency = :currency,
									photo = :photo, photo_big = :photo_big, photo_more = :photo_more,
									quantity = :quantity, new = :new, discount = :discount, hit = :hit, rating = :rating, ordering = :ordering, cdate = NOW()
								WHERE id = :id');

	$SQL_PREPARE->execute(
		array(
			'id' => $_ID,
			'identifier' => $_identifier,
			'section' => $_sectionID,
			'pub' => $_pub,
			'title' => $_title,
			'intro_text' => $_introText,
			'full_text' => $_fullText,
			'price' => $_price,
			'price_old' => $_priceOld,
			'currency' => $_currency,
			'photo' => $_photo,
			'photo_big' => $_photo,
			'photo_more' => $_gallery,
			'quantity' => $_quantity,
			'new' => $_new,
			'discount' => $_discount,
			'hit' => $_hit,
			'rating' => $_rating,
			'ordering' => $_ordering
		)
	);

	return true;
}

/*
	Обновить товар без изменения фотографий.
*/

function updateItemWithoutImage($_ID, $_title, $_sectionID, $_identifier, $_pub = 1,
						$_introText = '', $_fullText = '',
						$_price = 0, $_priceOld = 0, $_currency = 0,
						$_quantity = 1, $_new = 0, $_discount = 0, $_hit = 0, $_rating = 0,
						$_ordering = 0)
{
	global $db;

	$_ID = (int)$_ID;
	$_title = (string)$_title;
	$_sectionID = (int)$_sectionID;
	$_identifier = (string)$_identifier;
	$_pub = (int)$_pub;
	$_introText = (string)$_introText;
	$_fullText = (string)$_fullText;
	$_price = (string)$_price;
	$_priceOld = (string)$_priceOld;
	$_currency = (int)$_currency;
	$_quantity = (int)$_quantity;
	$_new = (int)$_new;
	$_hit = (int)$_hit;
	$_rating = (int)$_rating;
	$_discount = (int)$_discount;
	$_ordering = (int)$_ordering;

	$arraySection = \Shop\Sections\getSectionByID($_sectionID);

	if(count($arraySection) == 0 || strlen($_title) == 0)
		return false;

	$item = getItemByID($_ID);

	if(count($item) == 0)
		return false;

	$SQL_PREPARE = $db->prepare('UPDATE com_shop_item SET identifier = :identifier, section = :section, pub = :pub, parent = 1, title = :title,
									intro_text = :intro_text, full_text = :full_text, price = :price, price_old = :price_old, currency = :currency,
									quantity = :quantity, new = :new, discount = :discount, hit = :hit, rating = :rating, ordering = :ordering, cdate = NOW()
								WHERE id = :id');

	$SQL_PREPARE->execute(
		array(
			'id' => $_ID,
			'identifier' => $_identifier,
			'section' => $_sectionID,
			'pub' => $_pub,
			'title' => $_title,
			'intro_text' => $_introText,
			'full_text' => $_fullText,
			'price' => $_price,
			'price_old' => $_priceOld,
			'currency' => $_currency,
			'quantity' => $_quantity,
			'new' => $_new,
			'discount' => $_discount,
			'hit' => $_hit,
			'rating' => $_rating,
			'ordering' => $_ordering
		)
	);

	return true;
}

function deleteItemByID($_id)
{

}

function deleteItemByIdentifier($_identifier)
{
	global $db;

	$_identifier = (string)$_identifier;

	$SQL_PREPARE = $db->prepare('DELETE FROM com_shop_item WHERE identifier = :identifier');

	$SQL_PREPARE->execute(
		array(
			'identifier' => $_identifier
		)
	);
}

function getItemByID($_id)
{
	global $db;

	$_id = (int)$_id;

	$SQL_PREPARE = $db->prepare('SELECT
			i.id, i.section AS sectionID, m.id AS menuID, i.pub, i.identifier,
			i.title, i.intro_text, i.full_text,
			i.price, i.price_old, i.currency,
			i.photo, i.photo_big, i.photo_more,
			i.new, i.discount, i.hit, i.rating
		FROM com_shop_item AS i
		JOIN menu AS m
		WHERE i.section = m.id_com AND i.id = :id');

	$SQL_PREPARE->execute(
		array(
			'id' => $_id
		)
	);

	return $SQL_PREPARE->fetchAll();
}

function getItemByIdentifier($_identifier)
{
	global $db;

	$_identifier = (string)$_identifier;

	$SQL_PREPARE = $db->prepare('SELECT
			i.id, i.section AS sectionID, m.id AS menuID, i.pub, i.identifier,
			i.title, i.intro_text, i.full_text,
			i.price, i.price_old, i.currency,
			i.photo, i.photo_big, i.photo_more,
			i.new, i.discount, i.hit, i.rating
		FROM com_shop_item AS i
		JOIN menu AS m
		WHERE i.section = m.id_com AND i.identifier = :identifier');

	$SQL_PREPARE->execute(
		array(
			'identifier' => $_identifier
		)
	);

	return $SQL_PREPARE->fetchAll();
}