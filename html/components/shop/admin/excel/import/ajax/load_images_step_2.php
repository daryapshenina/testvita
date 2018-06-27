<?php
defined('AUTH') or die('Restricted access');

include_once $_SERVER['DOCUMENT_ROOT']."/components/shop/admin/classes/Photo.php";

if(!isset($_POST['id']))
	exit('ID не был передан.');

if(!isset($_FILES['images']))
	exit();

/**/

$ID = $_POST['id'];
$IMAGES = $_FILES['images'];

/**/

Shop\Photo\updatePhoto($ID, $IMAGES);
exit();
