<?php
namespace Shop;
defined('AUTH') or die('Restricted access');

include_once $_SERVER['DOCUMENT_ROOT']."/db.php";

/*
	Удалить все фотографии
*/

function deleteAllPhotos()
{
	global $db;

	$db->query('UPDATE com_shop_item SET photo = "", photo_big = "", photo_more = ""');

	if(file_exists($_SERVER['DOCUMENT_ROOT'].'/components/shop/photo'))
	{
		foreach (glob($_SERVER['DOCUMENT_ROOT'].'/components/shop/photo/*.jpg') as $file)
			@unlink($file);
	}
}


/*
	Удалить все характеристики и фильтры
*/

function deleteAllChars()
{
	global $db;

	$db->query('DELETE FROM com_shop_char_name');
	$db->query('DELETE FROM com_shop_char');
	$db->query('DELETE FROM com_shop_filter');
}

// Удалить сопутствующие товары
function deleteAllRelated()
{
	global $db;

	$db->query('DELETE FROM com_shop_related_item');
}


/*
	Очистить полностью интернет - магазин
*/

function deleteAll()
{
	global $db;

	$db->exec('DELETE FROM com_shop_char_name');
	$db->exec('ALTER TABLE com_shop_char_name auto_increment = 1;');
	$db->exec('DELETE FROM com_shop_char');
	$db->exec('ALTER TABLE com_shop_char auto_increment = 1;');	
	$db->exec('DELETE FROM com_shop_filter');
	$db->exec('ALTER TABLE com_shop_filter auto_increment = 1;');	
	$db->exec('DELETE FROM com_shop_item');
	$db->exec('ALTER TABLE com_shop_item auto_increment = 1;');	
	$db->exec('DELETE FROM com_shop_related_item');
	$db->exec('ALTER TABLE com_shop_related_item auto_increment = 1;');
	$db->exec('DELETE FROM com_shop_price_item');
	$db->exec('ALTER TABLE com_shop_price_item auto_increment = 1;');	
	$db->exec('DELETE FROM com_shop_section');
	$db->exec('ALTER TABLE com_shop_section auto_increment = 1;');
	$db->exec('DELETE FROM menu WHERE component = "shop" AND p1 = "section"');

	if(file_exists($_SERVER['DOCUMENT_ROOT'].'/components/shop/photo'))
	{
		foreach (glob($_SERVER['DOCUMENT_ROOT'].'/components/shop/photo/*.jpg') as $file)
			@unlink($file);
	}
}
