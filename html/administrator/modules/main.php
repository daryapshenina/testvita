<?php
// Выводит модули сайта в центре (компонентом)
defined('AUTH') or die('Restricted access');

if(file_exists($root.'/modules/'.$d[2].'/admin/main.php'))
{
	include_once($root.'/modules/'.$d[2].'/admin/main.php');
}
else // действие, если конкретный модуль не найден
{
		switch ($d[2]) {
		case "add":
			include_once($root.'/administrator/modules/add.php');
			break;
		case "copy":
			include_once($root.'/administrator/modules/copy.php');
			break;			
		case "delete":
			include_once($root.'/administrator/modules/delete.php');
			break;				
		case "up":
			include_once($root.'/administrator/modules/up.php');
			break;
		case "down":
			include_once($root.'/administrator/modules/down.php');
			break;
		case "pub_all":
			include_once($root.'/administrator/modules/pub_all.php');
			break;
		case "pub_pc":
			include_once($root.'/administrator/modules/pub_pc.php');
			break;
		case "pub_mobile":
			include_once($root.'/administrator/modules/pub_mobile.php');
			break;	
		case "pub_no":
			include_once($root.'/administrator/modules/pub_no.php');
			break;				
		default:
			include_once($root.'/administrator/modules/all.php');
	}
}

?>