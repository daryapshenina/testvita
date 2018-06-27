<?php
defined('AUTH') or die('Restricted access');

include $_SERVER['DOCUMENT_ROOT']."/db.php";

$db->query('DELETE FROM com_shop_char_name');
$db->query('DELETE FROM com_shop_char');
$db->query('DELETE FROM com_shop_filter');
$db->query('DELETE FROM com_shop_item');
$db->query('DELETE FROM com_shop_section');
$db->query('DELETE FROM menu WHERE component = "shop" AND p1 = "section"');

if(file_exists($_SERVER['DOCUMENT_ROOT'].'/components/shop/photo'))
{
	foreach (glob($_SERVER['DOCUMENT_ROOT'].'/components/shop/photo/*.jpg') as $file)
		unlink($file);
}

exit();
