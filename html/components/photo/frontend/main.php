<?php
defined('AUTH') or die('Restricted access');

include_once($root.'/components/photo/classes/settings.php');
$photo_settings = photoSettings::getInstance()->getValue();

if($frontend_edit == 1){$head->addFile('http://'.$domain.'/components/photo/frontend/section/edit.js');}

if($d[1] == 'section')
{
	switch ($d[2])
	{
		case 'like': include("components/photo/frontend/section/like.php"); break;	
		default: include("components/photo/frontend/section/main.php");		
	}
}
else
{
	header("HTTP/1.0 404 Not Found");
	include("404.php");
	exit;
}



?>
