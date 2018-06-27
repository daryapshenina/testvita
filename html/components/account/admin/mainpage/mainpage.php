<?php
defined('AUTH') or die('Restricted access');

$head->addFile('/components/account/admin/mainpage/tmp/mainpage.css');

$item_id = intval(isset($admin_d4)); 

function a_com()
{ 
	global $site, $item_id, $item_section_id ; 
	
	echo'
	<h1>Пользователи:</h1>
	<div class="account_settings_ico">
		<a href="/admin/com/account/users/all" class="account_settings_a">
			<img border="0" src="/components/account/admin/tmp/images/users.png" style="vertical-align: middle" />
			<br/>
			<span>Все пользователи</span>
		</a>
	</div>
	<div class="account_settings_ico">
		<a href="/admin/com/account/settings" class="account_settings_a">
			<img border="0" src="/components/account/admin/mainpage/tmp/settings.png" style="vertical-align: middle" />
			<br/>
			<span>Настройки</span>
		</a>
	</div>	
	';	
		
}
?>