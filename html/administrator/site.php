<?php
// DAN 2012
// выводит содержимое сайта в контенте (пункты меню) в админ панеле.

defined('AUTH') or die('Restricted access');

function a_com()
{ 
	global $root, $site, $admin_d2;
	
	// подключаем компоненты
	$compsql = mysql_query("SELECT * FROM `components` WHERE `enabled` = 1 ") or die ("Невозможно сделать выборку из таблицы - 1");	
	
	while($m = mysql_fetch_array($compsql)):
	$components_com = $m['components'];	
	include($root."/components/".$components_com."/admin/component.php"); 
	endwhile;	
	// --------------------

	echo '
		<div id="main-top"><b>Содержимое сайта:</b></div>
		<div>&nbsp;</div>
	';
	
	// верхнее меню
	echo '                  
		<table class="w100_bs1 menuheader">
			<tr>
				<td class="cell-v"></td>
				<td class="cell-title" >ВЕРХНЕЕ МЕНЮ / Наименование пункта меню</td>
				<td class="cell-compname" >Тип страницы</td>			
				<td class="cell-pub" title="Публикация. Зелёным цветом обозначены опубликованные пункты, серым - неопубликованные"align="center">Пб.</td>
			</tr>
		</table>
	';	
	
	echo '<table class="w100_bs1 '.$bold.'">';	
	tree('top',0,0);
	echo '</table>';	
	
	// левое меню
	echo '
		<div class="menusep"></div>
		<table class="w100_bs1 menuheader">
			<tr>
				<td class="cell-v" title="Поставьте галочку, если хотите совершить действие над этим пунктом"></td>
				<td class="cell-title" >ЛЕВОЕ МЕНЮ / Наименование пункта меню</td>
				<td class="cell-compname" >Тип страницы</td>			
				<td class="cell-pub" title="Публикация. Зелёным цветом обозначены опубликованные пункты, серым - неопубликованные"align="center">Пб.</td>
			</tr>
		</table>	
	';

	echo '<table class="w100_bs1">';
	tree('left',0,0);
	echo '</table>';

} // конец функции a_com


########### ФУНКЦИИ ##############################################################################################
// ======= ФУНКЦИЯ - РЕКУРСИЯ ДЛЯ ВЫВОДА ПУНКТОВ МЕНЮ И ПОДМЕНЮ (ГЛАВНОЕ МЕНЮ) =========================

function tree($menu_type, $i, $lvl) // $menu_type 1 - верхнее 2 - левое  $i = 0 начальный уровень меню, $lvl - уровень меню
{
global $site;
$lvl++;

$numtree_sql = "SELECT * FROM `menu` WHERE `menu_type` = '$menu_type' AND `parent` = '$i' ORDER BY `ordering` ASC";

$numtree = mysql_query($numtree_sql) or die ("Невозможно сделать выборку из таблицы - 3");
	
	$result = mysql_num_rows($numtree);
	
	if ($result > 0) 
	{
		$otstup = str_repeat("&nbsp;&nbsp;-&nbsp;&nbsp;",($lvl-1));  // отступ слева у пункта меню
	
		while($m = mysql_fetch_array($numtree)):
			$menu_id = $m['id'];
			$menu_name = $m['name'];
			$menu_description = $m['description'];	
			$menu_pub = $m['pub'];	
			$menu_parent = $m['parent'];
			$menu_ordering = $m['ordering'];
			$menu_component = $m['component'];
			$menu_main = $m['main'];
			$menu_p1 = $m['p1'];
			$menu_p2 = $m['p2'];
			$menu_p3 = $m['p3'];
			$menu_id_com = $m['id_com'];
			
			// первоначальный сброс 	
			$s_public = "";
			$cell_title = "";
			$ra = "";
			$lock = ''; 
			$access = '';					
		
			
			// ======= Подключаем раздел компонента =======
			
			// ----- PAGE -----
			if ($menu_component == "page")
			{
				$result_page_sql = mysql_query("SELECT * FROM `com_page` WHERE `id` = '$menu_id_com' LIMIT 1") or die ("Невозможно сделать выборку из таблицы - 3");	
				
				// --- опубликован ли раздел ---
				while($a = mysql_fetch_array($result_page_sql)):
					$page_access = $a['access'];
				endwhile;
				
				if ($page_access == 1)
				{
					$lock = '<img alt="страница закрыта паролем" title="страница закрыта паролем" src="http://'.$site.'/administrator/tmp/images/access.png">'; 
					$access = '<span class="red"> закрыта паролем</span>';
				}
			}
			// ----- / PAGE -----				

			
			// ----- ИНТЕРНЕТ - МАГАЗИН -----
			if ($menu_component == "shop")
			{
				$result_section_sql = mysql_query("SELECT * FROM `com_shop_section` WHERE `id` = '$menu_id_com' LIMIT 1") or die ("Невозможно сделать выборку из таблицы - 4");	
				
				// --- опубликован ли раздел ---
				while($a = mysql_fetch_array($result_section_sql)):
					$section_pub = $a['pub'];
				endwhile;
				
				// условия публикации пункта меню
				if ($menu_pub == "1") 
				{
					$cell_title = "раздел архива опубликован, пункт меню опубликован";
				}
				elseif ($menu_pub == "0" && isset($section_pub) == "1") 
				{
					$s_public = "spub";
					$cell_title = "раздел архива опубликован, но пункт меню скрыт"; // всплывающая подсказка
				}
				else 
				{
					$pub_x = '<img border="0" src="http://'.$site.'/administrator/tmp/images/p-unpub.gif" width="10" height="10">';
					$public = "menu_unpub";
					$cell_title = "раздел архива скрыт, пункт меню скрыт";
				}
				// --- /опубликован ли раздел ---	
				
				// определяем количество товаров в категории
				$result_shop_sql = mysql_query("SELECT * FROM `com_shop_item` WHERE `section` = '$menu_id_com' ORDER BY `ordering` ASC") or die ("Невозможно сделать выборку из таблицы - 5");
		
				$result_shop = mysql_num_rows($result_shop_sql);
				if ($result_shop != "" && $menu_main != "1" ){$ra = '<span class = "result-shop">('.$result_shop.')</span>';}
				else {$ra = "";}
			}
			// ----- / ИНТЕРНЕТ - МАГАЗИН -----	
			
			
			// ----- СТАТЬИ -----			
			if ($menu_component == "article")
			{
				$result_section_sql = mysql_query("SELECT * FROM `com_article_section` WHERE `id` = '$menu_id_com' LIMIT 1") or die ("Невозможно сделать выборку из таблицы - 6");	
				
				// --- опубликован ли раздел ---
				while($a = mysql_fetch_array($result_section_sql)):
					$section_pub = $a['pub'];
				endwhile;
				
				// условия публикации пункта меню
				if ($menu_pub == "1") 
				{
					$cell_title = "раздел архива опубликован, пункт меню опубликован";
				}
				elseif ($menu_pub == "0" && isset($section_pub) == "1") 
				{
					$s_public = "spub";
					$cell_title = "раздел архива опубликован, но пункт меню скрыт"; // всплывающая подсказка
				}
				else 
				{
					$pub_x = '<img border="0" src="http://'.$site.'/administrator/tmp/images/p-unpub.gif" width="10" height="10">';
					$public = "menu_unpub";
					$cell_title = "раздел архива скрыт, пункт меню скрыт";
				}
				// --- /опубликован ли раздел ---	
				
				
				// определяем количество статей в категории
				$result_articler_sql = mysql_query("SELECT * FROM `com_article_item` WHERE `section` = '$menu_id_com' ORDER BY `ordering` ASC") or die ("Невозможно сделать выборку из таблицы - 7");
		
				$result_article = mysql_num_rows($result_articler_sql);
				if ($result_article != "" && $menu_main != "1" ){$ra = '<span class = "result-article">('.$result_article.')</span>';}
				else {$ra = "";}				
			}
			// ----- / СТАТЬИ -----			
			
			
			// ----- ЦИТАТЫ -----			
			if ($menu_component == "quote")
			{
				$result_section_sql = mysql_query("SELECT * FROM `com_quote_section` WHERE `id` = '$menu_id_com' LIMIT 1") or die ("Невозможно сделать выборку из таблицы - 6");	
				
				// --- опубликован ли раздел ---
				while($a = mysql_fetch_array($result_section_sql)):
					$section_pub = $a['pub'];
				endwhile;
				
				// условия публикации пункта меню
				if ($menu_pub == "1") 
				{
					$cell_title = "раздел архива опубликован, пункт меню опубликован";
				}
				elseif ($menu_pub == "0" && $section_pub == "1") 
				{
					$s_public = "spub";
					$cell_title = "раздел архива опубликован, но пункт меню скрыт"; // всплывающая подсказка
				}
				else 
				{
					$pub_x = '<img border="0" src="http://'.$site.'/administrator/tmp/images/p-unpub.gif" width="10" height="10">';
					$public = "menu_unpub";
					$cell_title = "раздел архива скрыт, пункт меню скрыт";
				}
				// --- /опубликован ли раздел ---	
				
				
				// определяем количество статей в категории
				$result_quote_sql = mysql_query("SELECT * FROM `com_quote_item` WHERE `section_id` = '$menu_id_com' ORDER BY `ordering` ASC") or die ("Невозможно сделать выборку из таблицы - 7");
		
				$result_quote = mysql_num_rows($result_quote_sql);
				if ($result_quote != "" && $menu_p1 == "section" ){$ra = '<span class = "result-quote">('.$result_quote.')</span>';}
				else {$ra = "";}				
			}
			// ----- / ЦИТАТЫ -----						
			// ======= / Подключаем раздел компонента =======	
			
			
			
			// для url
			if ($menu_id_com != ""){$m_id_com = '/'.$menu_id_com;} else {$m_id_com = "";}			
			
			// дополнительные параметры
			if ($menu_p1 != ""){$p1 = '/'.$menu_p1;} else {$p1 = "";}
			if ($menu_p2 != ""){$p2 = '/'.$menu_p2;} else {$p2 = "";}
			if ($menu_p3 != ""){$p3 = '/'.$menu_p3;} else {$p3 = "";}
			
			$p = $p1.$p2.$p3;
			
			// тип меню
			if ($menu_type == "top"){$menu_t = 1;}
			if ($menu_type == "left"){$menu_t = 2;}
			
			// класс главной страницы
			if($menu_main == "1")
			{
				$main = "main";
				$bold = 'bold ';
				$ico = $menu_component.'main-ico';
			} 
			elseif ($menu_p1 == "authors") 
			{
				$main = 'authors';
				$bold = 'bold ';
				$ico = '';			
			}			
			else 
			{
				$main = '';
				$bold = '';
				$ico = '';			
			}	
			
			// строка компонента
			$com_class = $menu_component.'-bg';
			
			// условия публикации 
			if ($menu_pub == "1") 
			{
				$pub_x = '<img border="0" src="http://'.$site.'/administrator/tmp/images/p-pub.gif" width="10" height="10" title="опубликовано">';
				$public = "menu_pub";
				}
				else {
				$pub_x = '<img border="0" src="http://'.$site.'/administrator/tmp/images/p-unpub.gif" width="10" height="10" title="не опубликовано">';
				$public = "menu_unpub ".$s_public;
			}

			// выводим содержимое
				echo'			
					<tr>
						<td class="cell-ico '.$ico.' '.$com_class.' '.$bold.'"  title="'.$cell_title.'">'.$lock.'</td>
						<td class="cell-title '.$com_class.' '.$bold.'"  title="'.$cell_title.'" ><a class="'.$menu_component.$main.' '.$public.'" name="'.$menu_id_com.'/'.$menu_t.'"  href="http://'.$site.'/admin/com/'.$menu_component.$p.$m_id_com.'/'.$menu_t.'" >'.$otstup.$menu_name.'</a> '.$ra.'</td>						
						<td class="cell-compname  '.$public.' '.$com_class.' '.$bold.'"  title="'.$cell_title.'">'.$menu_description.$access.'</td>
						<td class="cell-pub '.$com_class.' '.$bold.'">'.$pub_x.'</td>
					</tr>			
				';		
	
			// рекурсия, выводим все пункты меню, для которых этот пункт является родительским
			tree($menu_type, $menu_id, $lvl); 
			
		endwhile;	
		
	} // конец проверки $result > 0
} // конец функции tree


?>
