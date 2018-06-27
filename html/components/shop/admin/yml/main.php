<?php
defined('AUTH') or die('Restricted access');

switch($admin_d4 . '/' . $admin_d5)
{

	/* import */

	case 'import/':
		include_once 'components/shop/admin/yml/import/view.php';
		break;

	case 'import/step_0':
		include_once 'components/shop/admin/yml/import/ajax/step_0.php';
		break;

	case 'import/step_1':
		include_once 'components/shop/admin/yml/import/ajax/step_1.php';
		break;

	case 'import/step_2':
		include_once 'components/shop/admin/yml/import/ajax/step_2.php';
		break;

	case 'delete/':
		include_once 'components/shop/admin/yml/delete.php';
		break;	

	/**/

	default:
		include_once 'components/shop/admin/yml/view.php';
		break;
}
