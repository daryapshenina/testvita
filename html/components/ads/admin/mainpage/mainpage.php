<?php
defined('AUTH') or die('Restricted access');

$head->addFile('/components/ads/admin/mainpage/tmp/mainpage.css');

$item_id = intval(isset($admin_d4)); 

function a_com()
{ 
	global $site, $item_id, $item_section_id ; 
	
	echo'
	<h1>Пользователи:</h1>
	<div class="ads_settings_ico">
		<a href="/admin/com/ads/section/add" class="ads_settings_a">
			<img border="0" src="/components/ads/admin/mainpage/tmp/section_add.png" style="vertical-align: middle" />
			<br/>
			<span>Добавить раздел</span>
		</a>
	</div>
	';
/*	
	echo '
	<div class="ads_settings_ico">
		<a href="/admin/com/ads/settings" class="ads_settings_a">
			<img border="0" src="/components/ads/admin/mainpage/tmp/settings.png" style="vertical-align: middle" />
			<br/>
			<span>Настройки</span>
		</a>
	</div>
	';	
*/	
}
?>