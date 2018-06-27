<?php
defined('AUTH') or die('Restricted access');

switch($admin_d4)
{
	case 'import':
		include_once 'components/shop/admin/excel/import/main.php';
		break;

	default:
		include_once 'components/shop/admin/excel/export/main.php';
		break;
}

if(!is_dir($root.'/temp/excel/'))
	mkdir($root.'/temp/excel/');

?>