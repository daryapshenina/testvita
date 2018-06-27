<?php
// Выводит список модулей слева

defined('AUTH') or die('Restricted access');

function a_modules_upgrade()
{
	global $domain, $latest_version_url;

	// загрузка XML-файла версии системы управления, установленной на сайте.
	$xmlversion = simplexml_load_file('tmp/version.xml');
	
	if ($xmlversion) 
	{	
		// версия системы управления сайтом
		$version = $xmlversion->cmsversion;
		$version = (real)$version;
	//	foreach ($xmlversion->theme as $item) 
	//	{	
	//		$version = $item->name;
	//	};
	}	
	
	// ----------------------------------------------------------------------------
	// загрузка XML-файла последней версии системы управления с удалённого сервера.
	
    // URL файла на сервере
    $xml_url = $latest_version_url.'latest.xml';
	// Имя файла для хранения xml на локальном сервере
    $export_latest = 'administrator/upgrade/export_latest.xml';
	
	// Закачка файла XML
   	// Если файла не существует (не закачан) или он устарел (свыше 24 часов) - закачать!
	
	if (!file_exists($export_latest) || time() > (filemtime($export_latest) + 60*60*24) ) 
	{
		// Закачать файл с указанного URL и сохранить с определенным именем
		$getxmlfile = file_get_contents($xml_url);
		if ($getxmlfile) file_put_contents($export_latest, $getxmlfile);
	}
	
	$xmllatest = simplexml_load_file('administrator/upgrade/export_latest.xml');
	
	if ($xmllatest) 
	{	
		// последняя версия системы управления сайтом
		$latest = $xmllatest->cmsversion;
		$latest =(real)$latest;
	}	
	
	//$version = "1.0";
	//$latest = "1.0";
	// ======= Проверка версии ====================================================================
	if (isset($xmllatest->cmsversion))
	{
		// проверка: установлена ли последняя версия сайта
		if ($version >= $latest) 
		{
			echo '<div id="top_5_latest">Версия '.$version.' (последняя)</div>';		
		}
		else 
		{
			echo '<div id="top_5_upgrade"><a class="white" href="/admin/upgrade/">Проверка обновления</a></div>';		
		}
	}
	else 
	{
		echo '<div id="top_5_latest">Обновление не найдено</div>';
	}
}

?>