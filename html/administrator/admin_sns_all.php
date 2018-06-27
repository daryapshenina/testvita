<?php
// DAN 2012
// Выводит все компоненты SNS
defined('AUTH') or die('Restricted access');


// определяем тип меню
$menu_t = intval($admin_d5);

// ------- Оределяем - какое меню надо редактировать и какую таблицу подключать ------
// сбрасываем значения
$menu_top_selected = "";
$menu_left_selected = "";

if (!isset($menu_t) || $menu_t == "0" || $menu_t == "1")
{
	$menu_type = "top";
	$menu_top_selected = "selected";	
}
if ($menu_t == "2")
{
	$menu_type = "left";
	$menu_left_selected = "selected";		
}


function a_com()
{ 
	global $site, $menu_t, $menu_type, $menu_top_selected, $menu_left_selected; 
	
	echo '
	<div id="main-top">ВСЕ КОМПОНЕНТЫ СОЦИАЛЬНОЙ СЕТИ:</div>
	<div style="margin:10px">
	';
	
	global $site, $section_id;

	// вывод содержимого меню	
	$num_com = mysql_query("SELECT * FROM `components` WHERE `enabled` = '3' ") or die ("Невозможно сделать выборку из таблицы - 1");
		
	while($c = mysql_fetch_array($num_com)):
		$components_id = $c['id'];
		$components_com = $c['components'];		
		$components_title = $c['title'];	
		$components_description = $c['description'];
		
		echo '<div><a id="'.$components_com.'-sns" href="http://'.$site.'/admin/sns/'.$components_com.'">'.$components_title.'</a></div>';
		
	endwhile;		
	
	echo'		
	</div>
	';

} // конец функции

?>