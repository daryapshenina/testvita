<?php
defined('AUTH') or die('Restricted access');

include_once __DIR__."/../csv.php";

$itemIdentifier = $_POST['itemIdentifier'];
$charName = $_POST['charName'];
$charValue = $_POST['charValue'];

if(strlen($itemIdentifier) == 0 || strlen($charName) == 0 || strlen($charValue) == 0)
	exit('Характеристика '.$charName.' : '.$charValue.' не добавлена для товара '.$itemIdentifier);

$characteristicName = CSV::addCharacteristicName($charName);
$responce = CSV::addItemCharacteristic($itemIdentifier, $charName, $charValue);

if(isset($responce) && $responce != null)
	echo $responce;

exit();
