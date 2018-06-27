<?php
defined('AUTH') or die('Restricted access');
include __DIR__."/../csv.php";

$type = (int)$_POST["type"];
$identifier = (string)$_POST["identifier"];
$identifier_parent = (string)$_POST["identifier_parent"];
$artikul = (string)$_POST["artikul"];
$title = (string)$_POST["title"];
$price = (int)$_POST["price"];
$photo = (string)$_POST["photo"];
$quantity = (int)$_POST["quantity"];

switch($type)
{
	case 1: // Sections
	{
		$responce = CSV::updateSection($identifier, $identifier_parent, $title);
	} break;

	case 0: // Items
	{
		$responce = CSV::updateItem($identifier, $identifier_parent, $title, $price, $photo, $quantity);
	} break;
}

if(isset($responce) && $responce != null)
	echo $responce;

exit();
