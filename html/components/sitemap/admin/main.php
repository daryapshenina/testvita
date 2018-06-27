<?php
// DAN 2015
defined('AUTH') or die('Restricted access');

$head->addFile('/components/sitemap/admin/tmp/style.css');

function a_com()
{
	global $domain;

	echo '<div class="main">
		<h1>Sitemap.xml</h1>
		Компонент sitemap помогает поисковым системам индексировать Ваш сайт.
		<br /><br />
		Карта сайта находится по адресу <a href="/sitemap.xml" target="_blank">/sitemap.xml</a> и генерируется динамически при каждом запросе что позволяет поисковым системам получать самую свежую версию.
	</div>';
}
