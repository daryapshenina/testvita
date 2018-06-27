<?php
// DAN 2011
// выводит содержимое сайта в контенте (пункты меню) в админ панеле.

defined('AUTH') or die('Restricted access');

function a_com()
{ 
global $site, $root, $admin_d2;

include($root."/components/article/admin/component.php");

echo '
<table id="main-top-tab">
	<tr>
		<td class="article_all_title">Архив статей</td>
		<td class="article_addsection"><a href="http://'.$site.'/admin/com/article/sectionadd/">Добавить раздел</a></td>
		<td class="article_additem"><a href="http://'.$site.'/admin/com/article/itemadd/">Добавить статью</a></td>
	</tr>
</table>
';

echo '                   
<table class="w100_bs1 menuheader">
	<tr>
		<td class="cell-v" title="Поставьте галочку, если хотите совершить действие над этим пунктом"></td>
		<td  class="cell-title" >Наименование раздела / товара</td>
		<td  class="cell-compname" >Тип</td>			
		<td  class="cell-pub" title="Публикация. Зелёным цветом обозначены опубликованные пункты, серым - неопубликованные"align="center">Пб.</td>
	</tr>
</table>	
';
	
tree(0,0);

} // конец функции a_com




########### ФУНКЦИИ ##############################################################################################
// ======= ФУНКЦИЯ - РЕКУРСИЯ ДЛЯ ВЫВОДА ПУНКТОВ МЕНЮ И ПОДМЕНЮ (ГЛАВНОЕ МЕНЮ) =========================

function tree($i, $lvl) // $i = 0 начальный уровень меню, $lvl - уровень меню
{ 
global $site;
global $lvl; // уровень
$lvl++;

$numtree = mysql_query("SELECT * FROM `menu` WHERE `parent`='$i' ORDER BY `ordering` ASC") or die ("Невозможно сделать выборку из таблицы - 2");

	$otstup = str_repeat("&nbsp;&nbsp;-&nbsp;&nbsp;",($lvl-1));  // отступ слева у пункта меню
	
	$result = mysql_num_rows($numtree);
	
	if ($result > 0) {
	
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
		
		if ($menu_id_com != ""){$menu_id_com_pt = '/'.$menu_id_com;} else {$menu_id_com_pt = "";}
		
		// дополнительные параметры
		if ($menu_p1 != ""){$p1 = '/'.$menu_p1;} else {$p1 = "";}
		if ($menu_p2 != ""){$p2 = '/'.$menu_p2;} else {$p2 = "";}
		if ($menu_p3 != ""){$p3 = '/'.$menu_p3;} else {$p3 = "";}
		
		$p = $p1.$p2.$p3;
		
		// класс главной страницы
		if($menu_main == "1")
		{
			$main = "main";
			$bold = 'bold ';
			$ico = $menu_component.'main-ico';
		} 
		else 
		{
			$main = '';
			$bold = '';
			$ico = '';			
		}	
		
		// строка компонента
		$com_class = $menu_component.'-bg';
		
		
		// --- ОПУБЛИКОВАН ЛИ РАЗДЕЛ ---	
		$result_section_sql = mysql_query("SELECT * FROM `com_article_section` WHERE `id` = '$menu_id_com' ORDER BY `ordering` ASC") or die ("Невозможно сделать выборку из таблицы - 4");
		
		// определяем - опубликован ли раздел
		while($a = mysql_fetch_array($result_section_sql)):
			$section_pub = $a['pub'];
		endwhile;
		
		// условия публикации пункта меню
		if ($menu_pub == "1") 
		{
			$pub_x = '<img border="0" src="http://'.$site.'/administrator/tmp/images/p-pub.gif" width="10" height="10">';
			$public = "menu_pub";
			$cell_title = "раздел архива опубликован, пункт меню опубликован";
		}
		elseif ($menu_pub == "0" && $section_pub == "1") 
		{
			$pub_x = '<img class="spub" border="0" src="http://'.$site.'/administrator/tmp/images/p-unpub.gif" width="10" height="10">';
			$public = "menu_unpub spub";
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
		$result_articler_sql = mysql_query("SELECT * FROM `com_article_item` WHERE `section` = '$menu_id_com' ORDER BY `ordering` ASC") or die ("Невозможно сделать выборку из таблицы - 4");
		
		$result_article = mysql_num_rows($result_articler_sql);
		if ($result_article != "" && $menu_main != "1" ){$ra = '<span class = "result-article">('.$result_article.')</span>';}
		else {$ra = "";}	
		
		// выводим только компонент архива статей
		if ($menu_component == 'article')
		{
			// выводим содержимое
			echo '			
				<table class="w100_bs1 '.$com_class.' '.$bold.'">		
					<tr>
						<td class="cell-ico '.$ico.'" title="'.$cell_title.'"></td>
						<td class="cell-title"><a class="'.$menu_component.$main.' '.$public.'" name="'.$menu_id_com.'"  href="http://'.$site.'/admin/com/'.$menu_component.$p.$menu_id_com_pt.'" ><b>'.$otstup.$menu_name.'</b> '.$ra.'</a></td>
						<td class="cell-compname" title="'.$cell_title.'" ><span class="'.$public.'">'.$menu_description.'</span></td>
						<td class="cell-pub" title="'.$cell_title.'" >'.$pub_x.'</td>
					</tr>
				</table>		
			';		
		}			

		// рекурсия, выводим все пункты меню, для которых этот пункт является родительским
		tree($menu_id, $lvl); 
		$lvl--;
		
	endwhile;	
		
	} // конец проверки $result > 0
} // конец функции tree

?>