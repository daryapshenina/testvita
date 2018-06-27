<?php
// Статус 0 - не активирован; 1 - активный; 13 - заблокирован;
defined('AUTH') or die('Restricted access');

switch ($d[1]) 
{
	case '': include $root."/components/account/frontend/mainpage/mainpage.php" ; break;
	case 'form': include $root."/components/account/frontend/registration/form.php" ; break;	
	case 'reg': include $root."/components/account/frontend/registration/reg.php" ; break;
	case 'login': include $root."/components/account/frontend/login/login.php" ; break;
	case 'logout': include $root."/components/account/frontend/login/logout.php" ; break;	
	case 'activation': include $root."/components/account/frontend/registration/activation.php" ; break;
	case 'all': include $root."/components/account/frontend/profile/all.php" ; break;
	case 'my': include $root."/components/account/frontend/profile/my.php" ; break;
	case 'view': include $root."/components/account/frontend/profile/view.php" ; break;
	case 'edit': include $root."/components/account/frontend/profile/edit.php" ; break;
	case 'update': include $root."/components/account/frontend/profile/update.php" ; break;
	case 'restore': 
		switch ($d[2])
		{
			case 'send': include $root."/components/account/frontend/restore/send.php" ; break;
			case 'check': include $root."/components/account/frontend/restore/check.php" ; break;
			case 'new': include $root."/components/account/frontend/restore/new.php" ; break;
			default: include $root."/components/account/frontend/restore/form.php";				
		}
		break;
	default: 
		Header ("Location: http://".$domain."/frontend/site"); 
		header("HTTP/1.0 404 Not Found");
		include("404.php");
	exit;		
}

?>