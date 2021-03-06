<?php
// DAN 2012
// выводит раздел архива статей
defined('AUTH') or die('Restricted access');

$section_id = intval($d[2]);

// ID активного меню
$active_menu = $section_id;

// вывод настроек	
$settings_sql = mysql_query("SELECT * FROM `com_article_settings`") or die ("Невозможно сделать выборку из таблицы - 1");	
		
while($s = mysql_fetch_array($settings_sql)):
	$article_id = $s['id'];
	$article_name = $s['name'];	
	$article_parametr = $s['parametr'];	

	
	if ($article_id == '1')
	{
		$article_title = $article_name;	
		$article_description = $article_parametr;
	}
	
	if ($article_name == 'tag_title')
	{
		$tag_title = $article_parametr;	
	}
	
	if ($article_name == 'tag_description')
	{
		$tag_description = $article_parametr;	
	}		
	
endwhile;


// ####### Функция вывода ##########################################################
function component()
{ 
	global $root, $site, $section_id, $article_title, $article_description, $section_sql;
	
	// Подключаем шаблон вывода заголовка архива статей
	include($root."/components/article/frontend/tmp/all_sections_title_tmp.php");
	
	// выводим дерево подкаталогов
	tree(0, 0);
	
} // конец функции component


// ####################################################################	
// ####### ФУНКЦИЯ - РЕКУРСИЯ ДЛЯ ВЫВОДА ПУНКТОВ МЕНЮ И ПОДМЕНЮ #######

function tree($i, $lvl) // $i = 0 начальный уровень меню, $lvl - уровень меню
{ 
global $site, $root, $menu_parent, $menu_type, $menu_id;  //global - уровень
$lvl++;

$numtree_sql = "SELECT * FROM `menu` WHERE `parent` = '$i' ORDER BY `ordering` ASC";

$numtree = mysql_query($numtree_sql) or die ("Невозможно сделать выборку из таблицы - 1");

	$otstup = str_repeat("&nbsp;-&nbsp;",($lvl -1));  // отступ слева у пункта меню
	
	$result = mysql_num_rows($numtree);
	
	if ($result > 0) {
	
	while($m = mysql_fetch_array($numtree)):
		$menu_id_tree = $m['id'];
		$menu_name_tree = $m['name'];
		$menu_component_tree = $m['component'];
		$menu_id_com_tree = $m['id_com'];
		$menu_type_tree = $m['menu_type'];
		$menu_p1_tree = $m['p1'];		
		if ($menu_type_tree == "top") {$menu_t = 1;}
		if ($menu_type_tree == "left") {$menu_t = 2;}
		
		if ($menu_component_tree == "article" && $menu_p1_tree == "section")
		{
			// ------- Проверяем, опубликован ли сам раздел -------
			
			$section_sql = mysql_query("SELECT * FROM `com_article_section` WHERE `id` = '$menu_id_com_tree';") or die ("Невозможно сделать выборку из таблицы - 2");		
			
			while($m = mysql_fetch_array($section_sql)):
				$section_id = $m['id'];	
				$section_pub = $m['pub'];	
				$section_title = $m['title'];
				
				// ----- Если раздел опубликован -----
				
				if ($section_pub == "1")
				{
					// --- Вывод статей ---
					
					$article_sql = "SELECT * FROM `com_article_item` WHERE `section` = '$section_id' AND `pub` = 1";
					
					$article_query = mysql_query($article_sql) or die ("Невозможно сделать выборку из таблицы - 2");
					
					// количество статей
					$number_articles = mysql_num_rows($article_query); 
					
					// подключаем шаблон вывода архива статей
					include($root."/components/article/frontend/tmp/all_sections_middle_tmp.php");
					
					// --- / Вывод статей ---					
				}
				
				// ----- / Если раздел опубликован -----	
			endwhile;					
		}			
		
		tree($menu_id_tree, $lvl); // рекурсия, выводим все пункты меню, для которых этот пункт является родительским
		
	endwhile;	
		
	} // конец проверки $result > 0
} // конец функции tree
?>