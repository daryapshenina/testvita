<?php
defined('AUTH') or die('Restricted access');

// регистрация
switch ($d[3])
{
	case 'item':
		switch ($d[4])
		{
			case 'delete': include($root."/components/ads/admin/item/delete.php"); break;
			case 'edit': include($root."/components/ads/admin/item/edit.php"); break;
			case 'pub': include($root."/components/ads/admin/item/pub.php"); break;			
			case 'unpub': include($root."/components/ads/admin/item/unpub.php"); break;
			case 'update': include($root."/components/ads/admin/item/update.php"); break;
			default :include($root."/components/ads/admin/mainpage/mainpage.php"); break;	
		}
	break;	
	case 'section':
		switch ($d[4])
		{
			case 'add': include($root."/components/ads/admin/section/edit.php"); break;
			case 'block': include($root."/components/ads/admin/section/ban.php"); break;
			case 'delete': include($root."/components/ads/admin/section/delete.php"); break;
			case 'edit': include($root."/components/ads/admin/section/edit.php"); break;
			case 'insert': include($root."/components/ads/admin/section/insert.php"); break;
			case 'unblock': include($root."/components/ads/admin/section/unban.php"); break;
			case 'update': include($root."/components/ads/admin/section/update.php"); break;
			case 'view': include($root."/components/ads/admin/section/view.php"); break;
			default :include($root."/components/ads/admin/section/section.php"); break;	
		}
	break;

	default :include($root."/components/ads/admin/mainpage/mainpage.php"); break;
}

?>