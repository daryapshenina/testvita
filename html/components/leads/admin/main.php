<?php
// определяет действие внутри компонента
// status: 0 => непросмотренный; 1 => просмотренный; 2 => помеченный;
defined('AUTH') or die('Restricted access');
switch ($d[3])
{
	case 'view':
		include($root."/components/leads/admin/view.php");
		break;
	case 'old':
		include($root."/components/leads/admin/old.php");
		break;
	case 'mark':
		include($root."/components/leads/admin/mark.php");
		break;
	case 'remove_mark':
		include($root."/components/leads/admin/remove_mark.php");
		break;		
	case 'delete':
		include($root."/components/leads/admin/delete.php");
		break;
	default:
		include($root."/components/leads/admin/mainpage.php");
}


?>