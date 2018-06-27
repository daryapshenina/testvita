<?php
namespace Shop\Photo;
defined('AUTH') or die('Restricted access');

include_once $_SERVER['DOCUMENT_ROOT']."/db.php";
include_once $_SERVER['DOCUMENT_ROOT']."/components/shop/classes/classShopSettings.php";
include_once __DIR__.'/Items.php';

/*
	Обновляет фотографию и галлерею у товара.

	$_ID - ID товара
	$_files - массив $_FILES полученный из POST
*/

function updatePhoto($_ID, $_files)
{
	global $db, $shopSettings;

	$item = \Shop\Items\getItemByID($_ID);
	$itemNewPhotoBig = '';
	$itemNewPhotoSmall = '';
	$itemNewGallery = '';

	if(count($item) == 0 || !is_array($_files))
		return;

	/* удаление старых фотографий */
	$arrayOldPhoto = explode(';', $item[0]['photo_more']);
	$arrayOldPhoto[] = $item[0]['photo'];
	$arrayOldPhoto[] = $item[0]['photo_big'];

	foreach($arrayOldPhoto as $iter)
	{
		if(is_file($_SERVER['DOCUMENT_ROOT'].'/components/shop/photo/'.$iter))
			@unlink($_SERVER['DOCUMENT_ROOT'].'/components/shop/photo/'.$iter);
	}

	/* метод ресайза и новые размеры фотографий */
	$methodResize = $shopSettings->small_resize_method;

	$sizeBigX = $shopSettings->x_big;
	$sizeBigY = $shopSettings->y_big;

	$sizeSmallX = $shopSettings->x_small;
	$sizeSmallY = $shopSettings->y_small;

	/* перебор всех полученных изображений, обработка и составление списка для записи в БД */
	for($i = 0;$i < count($_files['tmp_name']);$i++)
	{
		$name = md5(((string)time()).((string)(rand(0, 1000))));
		$namePhotoBig = $name.'_.jpg';
		$namePhotoSmall = $name.'.jpg';

		// Получим размер изображения и его тип
		$size = getimagesize($_files['tmp_name'][$i]);

		$src_width = $size[0];
		$src_height = $size[1];

		// если "большое изображение" меньше "размера из настроек"
		if (($src_width <= $sizeBigX ) && ($src_height <= $sizeBigY))
		{
			$sizeBigX = $src_width;
			$sizeBigY = $src_height;
		}

		createImage($_files['tmp_name'][$i], $namePhotoBig, 0, $sizeBigX, $sizeBigY);
		createImage($_files['tmp_name'][$i], $namePhotoSmall, $methodResize, $sizeSmallX, $sizeSmallY);

		if($i === 0)
		{
			$itemNewPhotoBig = $namePhotoBig;
			$itemNewPhotoSmall = $namePhotoSmall;
		}
		else
		{
			$itemNewGallery .= $namePhotoSmall.';';
		}
	}

	/* запись в бд */
	$SQL_PREPARE = $db->prepare('UPDATE com_shop_item SET photo = :photo, photo_big = :photo_big, photo_more = :photo_more WHERE id = :id');

	$SQL_PREPARE->execute(
		array(
			'photo' => $itemNewPhotoSmall,
			'photo_big' => $itemNewPhotoBig,
			'photo_more' => $itemNewGallery,
			'id' => $_ID
		)
	);
}

/*

*/

function deletePhoto($_ID)
{
	global $db;

	$item = \Shop\Items\getItemByID($_ID);

	if(count($item) === 0)
		return;

	$arrayOldPhoto = explode(';', $item[0]['photo_more']);
	$arrayOldPhoto[] = $item[0]['photo'];
	$arrayOldPhoto[] = $item[0]['photo_big'];

	foreach($arrayOldPhoto as $iter)
		if(is_file($_SERVER['DOCUMENT_ROOT'].'/components/shop/photo/'.$iter))
			@unlink($_SERVER['DOCUMENT_ROOT'].'/components/shop/photo/'.$iter);

	$SQL_PREPARE = $db->prepare('UPDATE com_shop_item SET photo = "", photo_big = "", photo_more = "" WHERE id = :id');

	$SQL_PREPARE->execute(
		array(
			'id' => $_ID
		)
	);
}

/*

*/

function createImage($_pathToFile, $_newName, $_methodResize, $_sizeX, $_sizeY)
{
	switch($_methodResize)
	{
		case 2: // CUTTING
		{
			include_once($_SERVER['DOCUMENT_ROOT']."/classes/ImageResize/ImageResizeCutting.php");
			$img = new \ImageResizeCutting($_pathToFile, $_SERVER['DOCUMENT_ROOT'].'/components/shop/photo/'.$_newName, $_sizeX, $_sizeY);
			$img->run();
		} break;

		case 3: // COMPRESSION
		{
			include_once($_SERVER['DOCUMENT_ROOT']."/classes/ImageResize/ImageResizeCompression.php");
			$img = new \ImageResizeCompression($_pathToFile, $_SERVER['DOCUMENT_ROOT'].'/components/shop/photo/'.$_newName, $_sizeX, $_sizeY);
			$img->run();
		} break;

		case 1: // SMART
		{
			include_once($_SERVER['DOCUMENT_ROOT']."/classes/ImageResize/ImageResizeSmart.php");
			$img = new \ImageResizeSmart($_pathToFile, $_SERVER['DOCUMENT_ROOT'].'/components/shop/photo/'.$_newName, $_sizeX, $_sizeY);
			$img->run();
		} break;

		default: // JUST RESIZE
		{
			include_once($_SERVER['DOCUMENT_ROOT']."/classes/ImageResize/ImageResize.php");
			$img = new \ImageResize($_pathToFile, $_SERVER['DOCUMENT_ROOT'].'/components/shop/photo/'.$_newName, $_sizeX, $_sizeY);
			$img->run();
		}
	}
}

