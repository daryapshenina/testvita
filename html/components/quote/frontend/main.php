<?php
// DAN 2012
defined('AUTH') or die('Restricted access');

// === Вывод настроек архива статей =============================================
$articlesql = mysql_query("SELECT * FROM `com_quote_settings`") or die ("Невозможно сделать выборку из таблицы - 1");

while($a = mysql_fetch_array($articlesql)):
	$setting_id = $a['id'];	
	$setting_name = $a['name'];
	$setting_parametr = $a['parametr'];		
	
	// количество цитат выводимых в категории
	if ($setting_name == "quantity"){$quantity = $setting_parametr;} 
	
	// все авторы
	if ($setting_id == 3){$quote_title = $setting_name;} 
	
	// tag_title
	//if ($setting_name == "tag_title"){$tag_title = $setting_parametr;}
	
	// tag_description
	//if ($setting_name == "tag_description"){$tag_description = $setting_parametr;}		
	 
endwhile;	

// ===================================================================================

$act = $d[1];

// вывод всех разделов цитат
if ($act == "all"){include($root."/components/quote/frontend/quote_all_sections.php");}

// вывод раздела
elseif ($act == "section"){include($root."/components/quote/frontend/quote_section.php");}

// вывод всех авторов
elseif ($act == "authors"){include($root."/components/quote/frontend/quote_all_authors.php");}

// вывод цитат автора
elseif ($act == "author"){include($root."/components/quote/frontend/quote_author.php");}

// если прочая неопределённая хрень - вывод страницы ошибки
else {
	header("HTTP/1.0 404 Not Found"); 
	include("404.php");
	exit;	
} 

$head->addFile('http://'.$site.'/components/quote/frontend/tmp/style.css');

?>