<?php
defined('AUTH') or die('Restricted access');

include_once $_SERVER['DOCUMENT_ROOT']."/db.php";
include_once $_SERVER['DOCUMENT_ROOT']."/classes/Excel.php";

const PATH_TO_PRICE = '/temp/excel/price.xlsx';

$file = $_FILES['file'];

if(!isset($file) || $file['type'] !== 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet')
	exit('-1');

@mkdir($_SERVER['DOCUMENT_ROOT'].'/temp/excel/', 0777, true);

foreach(glob($_SERVER['DOCUMENT_ROOT'].'/temp/excel/*') as $iter)
	@unlink($iter);

move_uploaded_file($file['tmp_name'], $_SERVER['DOCUMENT_ROOT'].PATH_TO_PRICE);

$excel = new Excel($_SERVER['DOCUMENT_ROOT'].PATH_TO_PRICE);
echo $excel->getSizeY();

exit();
