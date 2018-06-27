<?php
// DAN 2010
// выводит содержимое сайта в контенте (пункты меню) в админ панеле.

defined('AUTH') or die('Restricted access');

function a_com()
{ 
global $site, $root, $admin_d2;

include($root."/components/shop/admin/component.php");

echo '
<table id="main-top-tab">
	<tr>
		<td class="imshop"><b>Интернет-магазин</b></td>
		<td class="addsection"><a href="http://'.$site.'/admin/com/shop/addsection/">Добавить раздел</a></td>
		<td class="additem"><a href="http://'.$site.'/admin/com/shop/additem/">Добавить товар</a></td>
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

$numtree = mysql_query("SELECT * FROM `menu` WHERE `parent`='$i' AND `component`='shop' ORDER BY `ordering` ASC") or die ("Невозможно сделать выборку из таблицы - 3");

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
		
		// дополнительные параметры
		if ($menu_p1 != ""){$p1 = $menu_p1.'/';} else {$p1 = "";}
		if ($menu_p2 != ""){$p2 = $menu_p2.'/';} else {$p2 = "";}
		if ($menu_p3 != ""){$p3 = $menu_p3.'/';} else {$p3 = "";}
		
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
		
		// условия публикации 
		if ($menu_pub == "1") 
		{
			$pub_x = '<img border="0" src="http://'.$site.'/administrator/tmp/images/p-pub.gif" width="10" height="10" title="опубликовано">';
			$public = "menu_pub";
			}
			else {
			$pub_x = '<img border="0" src="http://'.$site.'/administrator/tmp/images/p-unpub.gif" width="10" height="10" title="не опубликовано">';
			$public = "menu_unpub";
		}
		
		// определяем количество товаров в категории
		$resulttovarsql = mysql_query("SELECT * FROM `com_shop_item` WHERE `section` = '$menu_id_com' ORDER BY `ordering` ASC") or die ("Невозможно сделать выборку из таблицы - 4");
		
		$resulttovr = mysql_num_rows($resulttovarsql);
		if ($resulttovr != "" && $menu_main != "1" ){$rt = '<span class = "resulttovar">('.$resulttovr.')</span>';}
		else {$rt = "";}		
		
		// выводим содержимое
			echo'			
				<table class="w100_bs1 '.$com_class.' '.$bold.'">		
					<tr>
						<td class="cell-ico '.$ico.'"></td>
						<td class="cell-title "><a class="'.$menu_component.$main.' '.$public.'" name="'.$menu_id_com.'"  href="http://'.$site.'/admin/com/'.$menu_component.'/'.$p.$menu_id_com.'/" ><b>'.$otstup.$menu_name.'</b> '.$rt.'</a></td>
						<td class="cell-compname  '.$public.'">'.$menu_description.'</td>
						<td class="cell-pub">'.$pub_x.'</td>
					</tr>
				</table>			
			';		

		// рекурсия, выводим все пункты меню, для которых этот пункт является родительским
		tree($menu_id, $lvl); 
		$lvl--;
		
	endwhile;	
		
	} // конец проверки $result > 0
} // конец функции tree

?>