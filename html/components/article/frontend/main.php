<?php
// DAN 2012
defined('AUTH') or die('Restricted access');

// === Вывод настроек архива статей =============================================
$articlesql = mysql_query("SELECT * FROM `com_article_settings`") or die ("Невозможно сделать выборку из таблицы - 1");

while($a = mysql_fetch_array($articlesql)):
	$setting_id = $a['id'];	
	$setting_name = $a['name'];
	$setting_parametr = $a['parametr'];		
	
	// Описание архива статей
	if ($setting_id == 1){$article_description = $setting_parametr;} 
	
	// количество выводимых статей в категории
	if ($setting_name == "quantity"){$quantity = $setting_parametr;} 
	 
endwhile;	

// ===================================================================================

$act = $d[1];
$task = $d[2];

// вывод всех разделов интернет-магазина
if ($act == "all"){include($root."/components/article/frontend/article_all_sections.php");}
elseif ($act == "section"){include($root."/components/article/frontend/article_section.php");} // вывод раздела
elseif ($act == "item"){include($root."/components/article/frontend/article_item.php");} // вывод товара
else // если прочая неопределённая хрень - вывод страницы ошибки
{
	header("HTTP/1.0 404 Not Found"); 
	include("404.php");
	exit;	
} 

$head->addFile('/components/article/frontend/tmp/style.css');
?>