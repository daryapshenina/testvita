<?php
// подключаем настройки сайта

if (isset($_COOKIE['theme']))
{
	$theme = zapros($_COOKIE['theme']);
}
else
{
	$theme = 'default';
}

$query_settings = $db->query("SELECT * FROM settings");
			
while($s = $query_settings->fetch())
{
	$settings_id = $s['id'];
	$settings_parameter = $s['parameter'];	
	
	// Сайт включён / выключен
	if ($s['id'] == "1"){$settings_enable = $s['parameter'];}
	
	// Title сайта
	if ($s['id'] == "2"){$site_title = $s['parameter'];}
	
	// Description сайта
	if ($s['id'] == "3"){$site_description = $s['parameter'];}	
	
	// Тема сайта
	if ($s['id'] == "4"){$settings_theme = $s['parameter'];}		
	
	// Код счётчика статистики
	if ($s['id'] == "5"){$statistics = $s['parameter'];}

	// Код счётчика статистики
	if ($s['id'] == "7"){$meta_tag = $s['parameter'];}
	
}	

if ($settings_enable != "1")
{
	echo '
	<p align="center">
	<img border="0" src="http://'.$domain.'/settings/closed.jpg" width="1000" height="500"></p>
	'; 
	
	exit;
} 


// если тег "title" не заполнен - подставляем значение по-умолчанию
if (!isset($tag_title) || strlen($tag_title) < 3)
{
	if (isset($page_title))
	{
		$tag_title = $page_title.' - '.$site_title;
	}
	else
	{
		$tag_title = $site_title;
	}
}
			
function set_title()
{ 
	global $tag_title; 
	
	$search = array ("'<script[^>]*?>.*?</script>'si",  // Вырезает javaScript 
					 "'<[\/\!]*?[^<>]*?>'si",           // Вырезает HTML-теги 
					 "'([\r\n])[\s]+'",                 // Вырезает пробельные символы 
					 "'&(quot|#34);'i",                 // Заменяет HTML-сущности 
					 "'&(amp|#38);'i", 
					 "'&(lt|#60);'i", 
					 "'&(gt|#62);'i", 
					 "'&(nbsp|#160);'i", 
					 "'&(iexcl|#161);'i", 
					 "'&(cent|#162);'i", 
					 "'&(pound|#163);'i", 
					 "'&(copy|#169);'i", 
					 "'&#(\d+);'");                    // интерпретировать как php-код 

	$tag_title = preg_replace($search, '', $tag_title); 	
	
	echo $tag_title;
} 

// если тег "description" не заполнен - подставляем значение по-умолчанию
if (!isset($tag_description) || strlen($tag_description) < 5)
{
	$tag_description = $site_description;
}

function set_description()
{ 
	global $tag_description;
	$search = array ("'<script[^>]*?>.*?</script>'si",  // Вырезает javaScript 
					 "'<[\/\!]*?[^<>]*?>'si",           // Вырезает HTML-теги 
					 "'([\r\n])[\s]+'",                 // Вырезает пробельные символы 
					 "'&(quot|#34);'i",                 // Заменяет HTML-сущности 
					 "'&(amp|#38);'i", 
					 "'&(lt|#60);'i", 
					 "'&(gt|#62);'i", 
					 "'&(nbsp|#160);'i", 
					 "'&(iexcl|#161);'i", 
					 "'&(cent|#162);'i", 
					 "'&(pound|#163);'i", 
					 "'&(copy|#169);'i", 
					 "'&#(\d+);'");                    // интерпретировать как php-код 

	$tag_description = preg_replace($search, '', $tag_description); 	
	
	echo $tag_description;
}

if ($theme == 'default' || !isset($theme) || $theme == '')
{
	// тема установленная в админке.
	function set_theme()
	{ 
		global $settings_theme;
	
		if ($settings_theme == 'default' || $settings_theme == '')
		{
			$theme_out = '';
		} 
		else 
		{	
			$theme_out = $settings_theme.'/';
		}
		
		echo $theme_out;
	} 	
}
else 
{
	// тема установленная в frontend.
	function set_theme()
	{ 
		global $settings_theme, $theme;
	
		if ($settings_theme == 'default' || $settings_theme == '')
		{
			$theme_out = '';
		} 
		else 
		{	
			$theme_out = $settings_theme.'/';
		}
		
		echo $theme.'/';
	} 
}




?>