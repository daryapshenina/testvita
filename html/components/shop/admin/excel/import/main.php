<?php
defined('AUTH') or die('Restricted access');

switch($admin_d5)
{
	case 'load_items':
		include_once 'components/shop/admin/excel/import/ajax/load_items.php';
		break;

	case 'load_related_items':
		include_once 'components/shop/admin/excel/import/ajax/load_related_items.php';
		break;

	case 'load_images_step_0':
		include_once 'components/shop/admin/excel/import/ajax/load_images_step_0.php';
		break;

	case 'load_images_step_1':
		include_once 'components/shop/admin/excel/import/ajax/load_images_step_1.php';
		break;

	case 'load_images_step_2':
		include_once 'components/shop/admin/excel/import/ajax/load_images_step_2.php';
		break;

	case 'delete_all_images':
		include_once 'components/shop/admin/excel/import/ajax/delete_all_images.php';
		break;

	case 'delete_all_chars':
		include_once 'components/shop/admin/excel/import/ajax/delete_all_chars.php';
		break;

	case 'delete_all':
		include_once 'components/shop/admin/excel/import/ajax/delete_all.php';
		break;

	case 'load_price':
		include_once 'components/shop/admin/excel/import/ajax/load_price.php';
		break;

	default:
		include_once 'components/shop/admin/excel/import/view.php';
		break;
}

?>