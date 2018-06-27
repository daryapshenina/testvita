<?php
defined('AUTH') or die('Restricted access');

include_once $root.'/components/photo/classes/settings.php';
$photo_settings = photoSettings::getInstance()->getValue();

// главный пункт
if($d[3] == "section")
{
	switch ($d[4])
	{
		case 'ordering': include("components/photo/admin/section/ordering.php"); break;
		case 'add': include("components/photo/admin/section/edit.php"); break;
		case 'edit': include("components/photo/admin/section/edit.php"); break;
		case 'insert': include("components/photo/admin/section/insert.php"); break;
		case 'update': include("components/photo/admin/section/update.php"); break;
		case 'delete': include("components/photo/admin/section/delete.php"); break;
		case 'pub': include("components/photo/admin/section/pub.php"); break;
		case 'unpub': include("components/photo/admin/section/unpub.php"); break;
		case 'up': include("components/photo/admin/section/up.php"); break;
		case 'down': include("components/photo/admin/section/down.php"); break;
		default: include("components/photo/admin/section/main.php");		
	}
}
elseif($d[3] == "item")
{
	switch ($d[4])
	{
		case 'add': include("components/photo/admin/item/add.php"); break;
		case 'edit': include("components/photo/admin/item/edit.php"); break;
		case 'delete': include("components/photo/admin/item/delete.php"); break;
		case 'insert': include("components/photo/admin/item/insert.php"); break;
		case 'update': include("components/photo/admin/item/update.php"); break;
		case 'pub': include("components/photo/admin/item/pub.php"); break;
		case 'unpub': include("components/photo/admin/item/unpub.php"); break;
		default: include("components/photo/admin/section/main.php");		
	}
}
elseif($d[3]== 'settings')
{
	switch ($d[4])
	{
		case 'update': include("components/photo/admin/settings/update.php"); break;
		default: include("components/photo/admin/settings/edit.php");		
	}
}
elseif($d[3]== 'frontend_update'){include("components/photo/admin/frontend_update.php"); exit;}
else {include("components/photo/admin/mainpage/mainpage.php");}

?>