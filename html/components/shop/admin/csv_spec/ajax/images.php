<?php
defined('AUTH') or die('Restricted access');

include_once __DIR__."/../csv.php";
include_once $_SERVER['DOCUMENT_ROOT']."/components/shop/classes/classShopSettings.php";

if(!isset($_FILES['image']))
{
	var_dump($_FILES['image']);
	exit();
	exit('Изображение не было передано');
}

$shopSettings = new classShopSettings();
$sizeBig['x'] = $shopSettings->getValue('x_big');
$sizeBig['y'] = $shopSettings->getValue('y_big');
$sizeSmall['x'] = $shopSettings->getValue('x_small');
$sizeSmall['y'] = $shopSettings->getValue('y_small');
$methodResize = $shopSettings->getValue('small_resize_method');

$responce = CSV::updatePhoto($_FILES['image'], $methodResize, $sizeBig, $sizeSmall);

if(isset($responce) && $responce != null)
	echo $responce;

exit();
