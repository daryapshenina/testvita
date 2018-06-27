<?php
defined('AUTH') or die('Restricted access');

if(Auth::check())
{
	switch ($d[1])
	{
		case 'my':
			switch($d[2])
			{
				case 'add': include $root."/components/ads/frontend/my/edit.php"; break;
				case 'delete': include $root."/components/ads/frontend/my/delete.php"; break;
				case 'edit': include $root."/components/ads/frontend/my/edit.php"; break;
				case 'insert': include $root."/components/ads/frontend/my/insert.php"; break;
				case 'update': include $root."/components/ads/frontend/my/update.php"; break;
				default: include $root."/components/ads/frontend/my/my.php"; break;				
			}
		break;
		case 'section': include $root."/components/ads/frontend/section/section.php"; break;
		case 'item': include $root."/components/ads/frontend/item/item.php"; break;
		default: include $root."/components/ads/frontend/my/my.php";
	}
}
else
{
	Header("Location: /account");
}


?>