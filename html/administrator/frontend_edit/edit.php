<?php
// DAN 2015
// frontend редактирование
defined('AUTH') or die('Restricted access');

$head->addFile('/administrator/frontend_edit/tmp/style.css');
$head->addFile('/administrator/plugins/ckeditor/ckeditor.js');
$head->addFile('/administrator/frontend_edit/js/edit.js');


function frontend_edit()
{
	global $domain;

	echo 
	'<div id="e_cp" class="e_cpanel">
		<div id="e_save_status" class="e_save_default"></div>	
		<a class="e_cpanel_ico e_cpanel_ico_exit " href="/admin/logout">
			<div class="e_cpanel_title">выход</div>
		</a>
		<a class="e_cpanel_ico e_cpanel_ico_viewsite " href="/admin/viewsite">
			<div class="e_cpanel_title">просмотр</div>
		</a>		
		<a class="e_cpanel_ico e_cpanel_ico_admin " href="/admin">
			<div class="e_cpanel_title">админ</div>
		</a>
		<a class="e_cpanel_ico e_cpanel_ico_modules " href="/admin/modules">
			<div class="e_cpanel_title">модули</div>
		</a>		
	</div>';
}

?>
