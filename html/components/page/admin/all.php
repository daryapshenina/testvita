<?php
// DAN 2012
// выводит содержимое сайта в контенте (пункты меню) в админ панеле.

defined('AUTH') or die('Restricted access');

function a_com()
{ 
	global $root, $site, $admin_d2;
	
	// подключаем компонент
	include($root."/components/page/admin/component.php"); 
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
				<td class="cell-title" >ВЕРХНЕЕ МЕНЮ</td>
				<td class="cell-compname" >Тип страницы</td>			
				<td class="cell-pub" title="Публикация. Зелёным цветом обозначены опубликованные пункты, серым - неопубликованные"align="center">Пб.</td>
			</td>
		</td>	
	';	
	
	tree('top',0,0);	
	
	// левое меню
	echo '
		<div class="menusep"></div> 
		<table class="w100_bs1 menuheader">
			<tr>
				<td class="cell-v" title="Поставьте галочку, если хотите совершить действие над этим пунктом"></td>
				<td class="cell-title" >ЛЕВОЕ МЕНЮ</td>
				<td class="cell-compname" >Тип страницы</td>			
				<td class="cell-pub" title="Публикация. Зелёным цветом обозначены опубликованные пункты, серым - неопубликованные"align="center">Пб.</td>
			</tr>
		</table>	
	';
	
	tree('left',0,0);

} // конец функции a_com


########### ФУНКЦИИ ##############################################################################################
// ======= ФУНКЦИЯ - РЕКУРСИЯ ДЛЯ ВЫВОДА ПУНКТОВ МЕНЮ И ПОДМЕНЮ (ГЛАВНОЕ МЕНЮ) =========================

function tree($menu_type, $i, $lvl) // $i = 0 начальный уровень меню, $lvl - уровень меню
{ 
global $site;
$lvl++;

$numtree_sql = "SELECT * FROM `menu` WHERE `menu_type` = '$menu_type' AND `parent` = '$i' ORDER BY `ordering` ASC";

$numtree = mysql_query($numtree_sql) or die ("Невозможно сделать выборку из таблицы - 3");

	if ($lvl < 1){$lvl = 1;}

	$otstup = str_repeat("&nbsp;&nbsp;-&nbsp;&nbsp;",($lvl-1));  // отступ слева у пункта меню
	
	$result = mysql_num_rows($numtree);
	
	if ($result > 0) {
		
		if ($menu_type == "top") {$mt = 1;}
		if ($menu_type == "left") {$mt = 2;}
			
		
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
				$pub_x = '<img border="0" src="/administrator/tmp/images/p-pub.gif" width="10" height="10" title="опубликовано">';
				$public = "menu_pub";
				}
				else {
				$pub_x = '<img border="0" src="/administrator/tmp/images/p-unpub.gif" width="10" height="10" title="не опубликовано">';
				$public = "menu_unpub";
			}
			
			// выводим только компонент страницы
			if ($menu_component == 'page')
			{
				// выводим содержимое
				echo'			
					<table class="w100_bs1 '.$com_class.' '.$bold.'">		
						<tr>
							<td class="cell-ico '.$ico.'"></td>
							<td class="cell-title "><a class="'.$menu_component.$main.' '.$public.'" name="'.$menu_id_com.'/'.$menu_t.'" href="/admin/com/'.$menu_component.$p.$m_id_com.'/'.$menu_t.'" >'.$otstup.$menu_name.'</a></td>								
							<td class="cell-compname  '.$public.'">'.$menu_description.'</td>
							<td class="cell-pub">'.$pub_x.'</td>
						</tr>
					</table>			
				';		
			}
			// рекурсия, выводим все пункты меню, для которых этот пункт является родительским
			tree($menu_type, $menu_id, $lvl); 
			$lvl--;
			
		endwhile;	
		
	} // конец проверки $result > 0
} // конец функции tree

?>