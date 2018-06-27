<?php
defined('AUTH') or die('Restricted access');

switch($admin_d5)
{
	case 'step_0':
		include_once 'components/shop/admin/excel/export/ajax/step_0.php';
		break;

	case 'step_1':
		include_once 'components/shop/admin/excel/export/ajax/step_1.php';
		break;

	case 'image_step_0':
		include_once 'components/shop/admin/excel/export/ajax/image_step_0.php';
		break;

	case 'image_step_1':
		include_once 'components/shop/admin/excel/export/ajax/image_step_1.php';
		break;

	default:
		include_once 'components/shop/admin/excel/export/view.php';
		break;
}

?>