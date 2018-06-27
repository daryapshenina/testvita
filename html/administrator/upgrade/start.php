<?php
// Вывод страницы обновления
defined('AUTH') or die('Restricted access');

function a_com()
{ 
	global $domain, $latest_version_url;
	
	// загрузка XML-файла версии системы управления, установленной на сайте.
	$xmlversion = simplexml_load_file('tmp/version.xml');
	
	if ($xmlversion) 
	{	
		// версия системы управления сайтом
		$version = $xmlversion->cmsversion;
		// переводим в цифровой формат
		$version = (real)$version;
	}	
	
	// ----------------------------------------------------------------------------
	// загрузка XML-файла последней версии системы управления с удалённого сервера.
	
    // URL файла на сервере
    $xml_url = $latest_version_url.'latest.xml';
	
	// Закачка файла XML
	$xmllatest = simplexml_load_file($xml_url);
	
	if ($xmllatest) 
	{	
		// последняя версия системы управления сайтом
		$latest = $xmllatest->cmsversion;
		$latest =(real)$latest;
	}	
	
	// проверка: установлена ли последняя версия сайта
	if ($version >= $latest) 
	{
		echo '
			<div id="main-top"><img border="0" src="/administrator/tmp/images/upgrade25.png" width="25" height="25"  align="middle"/>&nbsp;&nbsp;Обновление не требуется.</div>
			<div>&nbsp;</div>
			<div class="margin-left-right-10">
				<div>Текущая версия сайта <span class="red"><b>'.$version.'</b></span></div>
				<div>Текущая версия обновления <span class="red"><b>'.$latest.'</b></span></div>
				<h4 class="green">Обновление не требуется, у вас установлена последняя версия сайта</h4>			
			</div>	
		';		
		
	}
	else 
	{
		echo '
			<div id="main-top"><img border="0" src="/administrator/tmp/images/upgrade25.png" width="25" height="25"  align="middle"/>&nbsp;&nbsp;Установка обновления сайта</div>
			<div>&nbsp;</div>
			<div class="margin-left-right-10">
				<div>Текущая версия сайта <span class="red"><b>'.$version.'</b></span></div>
				<div>Текущая версия обновления <span class="red"><b>'.$latest.'</b></span></div>
				<h4><a class="red" href="/admin/upgrade/upgrade"><img border="0" src="/administrator/tmp/images/setup_red.png" width="306" height="83"  align="middle"/></a></h4>			
				<div>Для установки обновления потребуется некоторое время. Не закрывайте страницу.</div>
			</div>	
		';		
	}
	
} // конец функции компонента
?>