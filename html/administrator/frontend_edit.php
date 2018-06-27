<?php
// DAN 2015
// frontend редактирование
defined('AUTH') or die('Restricted access');

$head->addFile('http://'.$site.'/administrator/tmp/front_edit.css');
$front_edit = 1; // признак редактирования

function frontend_edit()
{
	echo '<div class="e_cpanel"></div>';
}

?>
