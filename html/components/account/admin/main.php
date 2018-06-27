<?php
defined('AUTH') or die('Restricted access');
// Статус 0 - не активирован; 1 - активный; 13 - заблокирован;

// регистрация
switch ($d[3])
{
	case 'users':
		switch ($d[4])
		{
			case 'all': include($root."/components/account/admin/users/all.php"); break; // Все профили
			case 'add':  // Добавить профиль
				$mode = 'add';
				include($root."/components/account/admin/users/edit.php");
				break;
			case 'ban': include($root."/components/account/admin/users/ban.php"); break;
			case 'delete': include($root."/components/account/admin/users/delete.php"); break;
			case 'edit':  //  профиль
				$mode = 'edit';
				include($root."/components/account/admin/users/edit.php");
				break;
			case 'insert': include($root."/components/account/admin/users/insert.php"); break;
			case 'unban': include($root."/components/account/admin/users/unban.php"); break;
			case 'update': include($root."/components/account/admin/users/update.php"); break;
			case 'view': include($root."/components/account/admin/users/view.php"); break;
		}
		break;
	case 'settings':
		switch ($d[4])
		{
			case 'update' :include($root."/components/account/admin/settings/update.php"); break; 
			default :include($root."/components/account/admin/settings/edit.php"); break; 
		}
		break;

	default :include($root."/components/account/admin/mainpage/mainpage.php"); break; // Главная страница 
}

?>