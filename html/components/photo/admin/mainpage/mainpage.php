<?php
defined('AUTH') or die('Restricted access');
$head->addFile('/components/photo/admin/mainpage/mainpage.css');

function a_com()
{
	global $domain;
	echo'
	<h1>Фотогалерея:</h1>
	<div class="photo_settings_ico">
		<a href="/admin/com/photo/section/add" class="photo_settings_a">
			<img border="0" src="/components/photo/admin/mainpage/images/section_add.png" style="vertical-align: middle" />
			<br/>
			<span>Добавить раздел</span>
		</a>
	</div>
	<div class="photo_settings_ico">
		<a href="/admin/com/photo/settings" class="photo_settings_a">
			<img border="0" src="/components/photo/admin/mainpage/images/settings.png" style="vertical-align: middle" />
			<br/>
			<span>Настройки</span>
		</a>
	</div>
	<div class="photo_settings_ico">
		<a href="" target="_blank" class="photo_settings_a">
			<img border="0" src="/components/photo/admin/mainpage/images/help.png" style="vertical-align: middle" />
			<br/>
			<span>Помощь</span>
		</a>
	</div>
	';	
}
?>