<?php
// DAN 2012
// выводит содержимое сайта в контенте (пункты меню) в админ панеле.

defined('AUTH') or die('Restricted access');

function a_com()
{ 
	global $root, $site, $admin_d2;
	
	echo '
		<h1>Компонент &quot;СТРАНИЦА&quot;</h1>
		<div class="bg_white" style="padding:20px;">	
			<div>Основной компонент  &quot;Страница&quot;.<div>		
			<div>Выводит содержимое страницы.</div>	
			<div>&nbsp;</div>
			<div>Можно размещать не только текст но и видео, фотогалерею, ссылки на документы, прайсы</div>
			<div>&nbsp;</div>
		</div>
	';	

	

} // конец функции a_com

?>